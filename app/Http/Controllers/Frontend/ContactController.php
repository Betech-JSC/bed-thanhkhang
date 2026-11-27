<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\File;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Http\Notifications\CommonNotification;
use App\Models\EmailTemplate;
use Inertia\Inertia;

class ContactController extends Controller
{
    public $model = Contact::class;

    public function index()
    {
        $faqs = [];

        try {
            if (Schema::hasTable('posts')) {
                $faqs = Post::query()
                    ->active()
                    ->where('type', Post::TYPE_FAQ)
                    ->orderByPosition()
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function (Post $post) {
                        $rawAnswer = $post->content ?: $post->description ?: '';
                        $cleanAnswer = trim(strip_tags((string) $rawAnswer));

                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'question' => $post->title,
                            'content' => $post->content ?: $post->description,
                            'answer' => $cleanAnswer !== '' ? $cleanAnswer : (string) $rawAnswer,
                        ];
                    })
                    ->values()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if (empty($faqs)) {
            $faqs = Post::getFrontendFaqs();
        }

        $data = [
            'faqs' => $faqs,
            'faqList' => $faqs,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Contact', $data);
    }

    public function store(Request $request)
    {
        try {
            $contact = $request->input('contact');
            if (!is_array($contact)) {
                $contact = $request->all();
            }
            if (isset($contact['contact']) && is_array($contact['contact'])) {
                $contact = $contact['contact'];
            }

            if (!isset($contact['data']) && $request->has('data')) {
                $contact['data'] = $request->input('data');
            }

            if (isset($contact['data']) && is_string($contact['data'])) {
                $decoded = json_decode($contact['data'], true);
                if (is_array($decoded)) {
                    $contact['data'] = $decoded;
                }
            }

            // Normalization: Tự động gom các trường vào mảng data cho cả payload phẳng lẫn lồng
            $data = is_array($contact['data'] ?? null) ? $contact['data'] : [];

            $name = $data['Họ và tên']
                ?? $data['name']
                ?? $contact['name']
                ?? $contact['Họ và tên']
                ?? $request->input('name')
                ?? $request->input('Họ và tên')
                ?? null;

            $phone = $data['Số điện thoại']
                ?? $data['phone']
                ?? $data['Phone']
                ?? $contact['phone']
                ?? $contact['Phone']
                ?? $contact['Số điện thoại']
                ?? $request->input('phone')
                ?? $request->input('Phone')
                ?? $request->input('Số điện thoại')
                ?? null;

            $email = $data['Email']
                ?? $data['email']
                ?? $contact['email']
                ?? $contact['Email']
                ?? $request->input('email')
                ?? $request->input('Email')
                ?? null;

            $service = $data['Dịch vụ quan tâm']
                ?? $data['service']
                ?? $contact['service']
                ?? $contact['Dịch vụ quan tâm']
                ?? $request->input('service')
                ?? $request->input('Dịch vụ quan tâm')
                ?? null;

            $content = $data['Nội dung cần hỗ trợ']
                ?? $data['content']
                ?? $data['note']
                ?? $contact['content']
                ?? $contact['note']
                ?? $contact['Nội dung cần hỗ trợ']
                ?? $request->input('content')
                ?? $request->input('note')
                ?? $request->input('Nội dung cần hỗ trợ')
                ?? null;

            $trimmedName = trim((string) ($name ?? ''));
            $trimmedPhone = trim((string) ($phone ?? ''));
            $trimmedEmail = trim((string) ($email ?? ''));

            if ($name !== null && !isset($data['Họ và tên'])) {
                $data['Họ và tên'] = $name;
            }
            if ($phone !== null && !isset($data['Số điện thoại'])) {
                $data['Số điện thoại'] = $phone;
            }
            if ($trimmedEmail !== '') {
                $data['Email'] = $trimmedEmail;
                if (isset($data['email'])) {
                    $data['email'] = $trimmedEmail;
                }
            } elseif ($email !== null && !isset($data['Email'])) {
                $data['Email'] = $trimmedEmail;
            }
            if ($service !== null) {
                $serviceTitle = is_array($service) ? ($service['title'] ?? '') : (string)$service;
                if (!isset($data['Dịch vụ quan tâm'])) {
                    $data['Dịch vụ quan tâm'] = $serviceTitle;
                }
                if (!isset($data['Service'])) {
                    $data['Service'] = is_array($service) ? $service : ['title' => $serviceTitle];
                }
            }
            if ($content !== null && !isset($data['Nội dung cần hỗ trợ'])) {
                $data['Nội dung cần hỗ trợ'] = $content;
            }

            $contactType = is_string($contact['type'] ?? null) && trim($contact['type']) !== ''
                ? trim($contact['type'])
                : 'CONTACT_FORM';

            // Safe, non-breaking server-side validation
            $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
            $isEn = $locale === 'en';
            $formSource = $data['form_source']
                ?? $contact['form_source']
                ?? $request->input('form_source')
                ?? null;
            $isAdviseOnlyPhone = ($contactType === 'ADVISE_FORM');
            $validationErrors = [];

            if (!$isAdviseOnlyPhone && $trimmedName === '') {
                $validationErrors['name'] = $isEn
                    ? 'Please enter your full name.'
                    : 'Vui lòng nhập họ và tên.';
            }

            if ($trimmedPhone === '') {
                $validationErrors['phone'] = $isEn
                    ? 'Please enter your phone number.'
                    : 'Vui lòng nhập số điện thoại.';
            } else {
                $digitsOnly = preg_replace('/\D+/', '', $trimmedPhone);
                if (strlen($digitsOnly) < 8 || strlen($digitsOnly) > 15) {
                    $validationErrors['phone'] = $isEn
                        ? 'Please enter a valid phone number (8-15 digits).'
                        : 'Số điện thoại không hợp lệ (8-15 chữ số).';
                }
            }

            if ($formSource === 'home_contact_section' && $trimmedEmail === '') {
                $validationErrors['email'] = $isEn
                    ? 'Please enter your email address.'
                    : 'Vui lòng nhập địa chỉ email.';
            } elseif ($trimmedEmail !== '' && !filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
                $validationErrors['email'] = $isEn
                    ? 'Please enter a valid email address.'
                    : 'Địa chỉ email không hợp lệ.';
            }

            $uploadedImages = $request->file('contact.images')
                ?? $request->file('images')
                ?? ($contact['images'] ?? null);

            $validatedImages = [];
            if (!empty($uploadedImages)) {
                $rawImages = is_array($uploadedImages) ? $uploadedImages : [$uploadedImages];
                $allowedMimeToExt = [
                    'image/jpeg' => ['jpg', 'jpeg'],
                    'image/png' => ['png'],
                    'image/webp' => ['webp'],
                    'image/gif' => ['gif'],
                ];
                $allowedImageTypes = [
                    IMAGETYPE_JPEG,
                    IMAGETYPE_PNG,
                    IMAGETYPE_WEBP,
                    IMAGETYPE_GIF,
                ];
                $maxBytes = 10 * 1024 * 1024; // 10 MB

                foreach ($rawImages as $uploadedFile) {
                    if (!$uploadedFile instanceof UploadedFile || !$uploadedFile->isValid()) {
                        $validationErrors['images'] = $isEn
                            ? 'Only valid image files (JPG, PNG, WEBP, GIF) up to 10MB are allowed.'
                            : 'Chỉ chấp nhận tệp hình ảnh hợp lệ (JPG, PNG, WEBP, GIF) tối đa 10MB.';
                        break;
                    }

                    $size = $uploadedFile->getSize();
                    $ext = strtolower((string) $uploadedFile->getClientOriginalExtension());
                    $mimeType = strtolower((string) $uploadedFile->getMimeType());
                    $realPath = $uploadedFile->getRealPath();
                    $imageInfo = ($realPath !== false && is_file($realPath)) ? @getimagesize($realPath) : false;

                    $isValidImage = is_int($size)
                        && $size > 0
                        && $size <= $maxBytes
                        && isset($allowedMimeToExt[$mimeType])
                        && in_array($ext, $allowedMimeToExt[$mimeType], true)
                        && is_array($imageInfo)
                        && isset($imageInfo[2])
                        && in_array($imageInfo[2], $allowedImageTypes, true);

                    if (!$isValidImage) {
                        $validationErrors['images'] = $isEn
                            ? 'Only valid image files (JPG, PNG, WEBP, GIF) up to 10MB are allowed.'
                            : 'Chỉ chấp nhận tệp hình ảnh hợp lệ (JPG, PNG, WEBP, GIF) tối đa 10MB.';
                        break;
                    }

                    $validatedImages[] = [
                        'file' => $uploadedFile,
                        'ext' => $ext === 'jpeg' ? 'jpg' : $ext,
                    ];
                }
            }

            if (!empty($validationErrors)) {
                $firstMessage = reset($validationErrors);
                if (!$request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
                    return response()->json([
                        'success' => false,
                        'message' => $firstMessage,
                        'errors' => $validationErrors,
                    ], 422);
                }

                return redirect()->back()->withErrors($validationErrors)->withInput();
            }

            $storedImages = null;
            if (!empty($validatedImages)) {
                $images = [];
                $disk = Storage::disk('uploads');
                foreach ($validatedImages as $item) {
                    $safeName = Str::uuid()->toString() . '.' . $item['ext'];
                    $storedPath = $disk->putFileAs('Contact', $item['file'], $safeName);
                    if ($storedPath) {
                        $images[] = [
                            'path' => str_replace('/static/', '', static_url($storedPath, [], false)),
                        ];
                    }
                }
                $storedImages = $images;
            }

            // Ưu tiên số 1: Lưu dữ liệu liên hệ an toàn vào database (chỉ truyền các trường công khai hợp lệ)
            $contactPayload = [
                'data' => $data,
                'type' => $contactType,
            ];
            if ($storedImages !== null) {
                $contactPayload['images'] = $storedImages;
            }

            $created = Contact::create($contactPayload);

            // Phản hồi tương thích: Nếu không phải Inertia và là API/Ajax -> trả lời JSON
            if (!$request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
                return response()->json([
                    'success'    => true,
                    'contact_id' => $created->id,
                ]);
            }

            // Nếu là Inertia request -> redirect back kèm flash success
            return redirect()->back()->withSuccess('success');
        } catch (\Throwable $th) {
            \Log::error('ContactController store error: ' . $th->getMessage(), [
                'exception' => $th,
            ]);

            $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
            $genericMessage = $locale === 'en'
                ? 'An error occurred while submitting your request. Please try again later.'
                : 'Đã có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại sau.';

            // Tuyệt đối không trả JSON cho Inertia request vì sẽ làm Inertia modal crash
            if (!$request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
                return response()->json(['success' => false, 'message' => $genericMessage], 500);
            }

            return redirect()->back()->withErrors(['error' => $genericMessage]);
        }
    }

    public function sendEmail($contact, $mailNotification = null)
    {
        try {
            if ($contact->status === Contact::STATUS_IS_SPAM) {
                $emails = explode(',', config('contact.mail_spam'));

                $data['mail_title'] = config('contact.message.new_spam', 'Thông báo nhận được Spam');
                $data = array_merge($data, $contact->data ?? []);
                $route = config('contact.types.' . $contact->type . '.route', 'contacts');
                $data['url'] = route(current_locale() . '.admin.' . $route . '.form', ['id' => $contact->id]);

                foreach ($emails as $email) {
                    if (!empty(trim($email))) {
                        Notification::route('mail', trim($email))
                            ->notify(new CommonNotification($data));
                    }
                }
            } else {
                $emails = empty($mailNotification) ? explode(',', notification_to()) : explode(',', $mailNotification);
                $contactData = $contact->data ?? [];
                $emailData = [
                    'mail_title' => config('contact.message.new_contact')
                ];

                if ($contact->type == 'CONTACT_FORM') {
                    $emailData = array_merge($emailData, [
                        'Name' => $contactData['Họ và tên'] ?? '',
                        'Phone' => $contactData['Số điện thoại'] ?? '',
                        'Email' => $contactData['Email'] ?? '',
                        'Note' => $contactData['Nội dung cần hỗ trợ'] ?? '',
                        'Service' => $contactData['Service']['title'] ?? '',
                        'Service link' => isset($contactData['Service']['slug']) ? route(current_locale() . '.services.show', ['slug' => $contactData['Service']['slug']]) : '',
                        'url' => route(current_locale() . '.admin.contacts.form', ['id' => $contact->id])
                    ]);
                } else if ($contact->type == 'APPLY_FORM') {
                    $files = [];

                    if (isset($contactData['File CV']) && is_array($contactData['File CV'])) {
                        foreach ($contactData['File CV'] as $index => $cv) {
                            $link = str_replace(' ', '%20', str_replace("/static/", "/storage/", config('app.url') . $cv));
                            $files['File CV ' . ($index + 1)] = 'File CV: <a href="' . $link . '">' . $link . '</a>';
                        }
                    }

                    $emailData = array_merge($emailData, [
                        'Name' => $contactData['Họ và tên'] ?? '',
                        'Phone' => $contactData['Phone'] ?? '',
                        'Email' => $contactData['Email'] ?? '',
                        'Job' => $contactData['Job']['title'] ?? '',
                        'Job link' => isset($contactData['Job']['slug']) ? route(current_locale() . '.jobs.show', ['slug' => $contactData['Job']['slug']]) : '',
                        'File cv' => $files,
                        'url' => route(current_locale() . '.admin.applies.form', ['id' => $contact->id])
                    ]);
                }

                foreach ($emails as $email) {
                    if (!empty(trim($email))) {
                        Notification::route('mail', trim($email))
                            ->notify(new CommonNotification($emailData));
                    }
                }
            }

            // send customer
            $data = $contact->data ?? [];
            if (method_exists($contact, 'transformEmailDetails')) {
                $transformedData = $contact->transformEmailDetails();
                if (is_array($transformedData)) {
                    $data = array_merge($data, $transformedData);
                }

                if (isset($data['File CV']['CV 1'])) {
                    $data['File CV']['CV 1'] = 'File CV: <a href="' . $data['File CV']['CV 1'] . '">' . $data['File CV']['CV 1'] . '</a>';
                }
            }

            $emailTo = $data['Email'] ?? $data['email'] ?? null;
            if (!empty($emailTo)) {
                $template = EmailTemplate::active()
                    ->where('type', $contact->type)
                    ->first();

                if ($template) {
                    $subject = $template->subject;
                    $content = $template->content;

                    // Map common aliases
                    $data['Name'] = $data['Name'] ?? $data['Họ và tên'] ?? $data['name'] ?? '';
                    $data['Email'] = $data['Email'] ?? $data['email'] ?? '';
                    $data['Phone'] = $data['Phone'] ?? $data['Số điện thoại'] ?? $data['phone'] ?? '';
                    $data['Note'] = $data['Note'] ?? $data['Nội dung cần hỗ trợ'] ?? $data['note'] ?? '';
                    $data['Service'] = $data['Service'] ?? $data['Dịch vụ'] ?? '';
                    $data['Job'] = $data['Job'] ?? $data['Công việc'] ?? '';

                    foreach ($data as $key => $value) {
                        if (is_string($value)) {
                            $subject = str_replace('{{' . $key . '}}', $value, $subject);
                            $content = str_replace('{{' . $key . '}}', $value, $content);
                        }
                    }

                    $data['mail_title'] = $subject;
                    $data['custom_body'] = $content;
                } else {
                    $data['mail_title'] = config('contact.message.success_form');
                }

                Notification::route('mail', $emailTo)
                    ->notify(new CommonNotification($data));
            }
        } catch (\Throwable $e) {
            \Log::error('ContactController sendEmail failed: ' . $e->getMessage(), [
                'contact_id' => $contact->id ?? null,
                'exception'  => $e,
            ]);
        }
    }
}
