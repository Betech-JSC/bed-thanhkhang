<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\File;
use Illuminate\Support\Facades\Notification;
use App\Http\Notifications\CommonNotification;
use App\Models\EmailTemplate;

class ContactController extends Controller
{
    public $model = Contact::class;

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

            if (isset($contact['images']) && !empty($contact['images'])) {
                $fileObj = new \App\Models\File('Contact');
                $fileUploaded = $fileObj->store($contact['images']);
                $images = [];
                if (isset($fileUploaded['successFiles'])) {
                    foreach ($fileUploaded['successFiles'] as $item) {
                        $images[] = [
                            'path' => str_replace('/static/', '', $item)
                        ];
                    }
                }
                $contact['images'] = $images;
            }

            $created = Contact::create($contact);

            if (!$request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
                return response()->json([
                    'success'    => true,
                    'contact_id' => $created->id,
                ]);
            }

            return redirect()->back()->withSuccess('success');
        } catch (\Throwable $th) {
            report($th);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
            }
            return redirect()->back()->withErrors(['error' => $th->getMessage()]);
        }
    }

    public function sendEmail($contact, $mailNotification = null)
    {
        if ($contact->status === Contact::STATUS_IS_SPAM) {
            $emails = explode(',', config('contact.mail_spam'));

            $data['mail_title'] = config('contact.message.new_spam', 'Thông báo nhận được Spam');
            $data = array_merge($data, $contact->data ?? []);
            $route = config('contact.types.' . $contact->type . '.route', 'contacts');
            $data['url'] = route(current_locale() . '.admin.' . $route . '.form', ['id' => $contact->id]);

            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new CommonNotification($data));
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
                Notification::route('mail', $email)
                    ->notify(new CommonNotification($emailData));
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
    }
}
