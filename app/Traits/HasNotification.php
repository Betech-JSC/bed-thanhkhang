<?php

namespace App\Traits;

use Illuminate\Support\Facades\Notification;
use App\Http\Notifications\CommonNotification;
use App\Models\Contact;
use App\Models\EmailTemplate;

trait HasNotification
{
    public static function bootHasNotification()
    {
        static::created(function ($model) {
            if (request()->route() === null) return;

            // 1. Send notification to admin (wrapped in try/catch to protect lead submission)
            try {
                if ($model->status === Contact::STATUS_IS_SPAM) {
                    $emails = array_filter(array_map('trim', explode(',', config('contact.mail_spam', ''))));

                    $data['mail_title'] = config('contact.message.new_spam', 'Thông báo nhận được Spam');
                    $data = array_merge($data, $model->data ?? []);

                    $route = config('contact.types.' . $model->type . '.route', 'contacts');
                    $data['url'] = route(current_locale() . '.admin.' . $route . '.form', ['id' => $model->id]);

                    foreach ($emails as $email) {
                        if (!empty($email)) {
                            Notification::route('mail', $email)
                                ->notify(new CommonNotification($data));
                        }
                    }
                } else {
                    $notificationTo = notification_to();
                    $emails = !empty($notificationTo) ? array_filter(array_map('trim', explode(',', $notificationTo))) : [];

                    $data = [
                        'mail_title' => config('contact.message.new_contact', 'Bạn nhận được liên hệ mới'),
                    ];

                    if (method_exists($model, 'transformEmail')) {
                        $data = array_merge($data, $model->transformEmail());
                    } elseif (is_array($model->data)) {
                        $data = array_merge($data, $model->data);
                    }

                    foreach ($emails as $email) {
                        if (!empty($email)) {
                            Notification::route('mail', $email)
                                ->notify(new CommonNotification($data));
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('HasNotification: Admin email notification failed: ' . $e->getMessage(), [
                    'contact_id' => $model->id ?? null,
                    'type'       => $model->type ?? null,
                    'exception'  => $e,
                ]);
            }

            // 2. Send confirmation to customer (wrapped in try/catch to protect lead submission)
            try {
                $data = $model->data ?? [];
                if (method_exists($model, 'transformEmailDetails')) {
                    $transformedData = $model->transformEmailDetails();
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
                        ->where('type', $model->type)
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
                        $data['mail_title'] = config('contact.message.success_form', 'Chúc mừng bạn gửi form thành công');
                    }

                    Notification::route('mail', $emailTo)
                        ->notify(new CommonNotification($data));
                }
            } catch (\Throwable $e) {
                \Log::error('HasNotification: Customer confirmation email failed: ' . $e->getMessage(), [
                    'contact_id' => $model->id ?? null,
                    'type'       => $model->type ?? null,
                    'exception'  => $e,
                ]);
            }
        });
    }
}
