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

            if ($model->status === Contact::STATUS_IS_SPAM) {
                $emails = [config('contact.mail_spam', '')];

                $data['mail_title'] = config('contact.message.new_spam', 'Thông báo nhận được Spam');

                $data = array_merge($data, $model->data);

                $route = config('contact.types.' . $model->type . '.route');

                $data['url'] = route(current_locale() . '.admin.' . $route . '.form', ['id' => $model->id]);

                foreach ($emails as $email) {
                    Notification::route('mail', $email)
                        ->notify(new CommonNotification($data));
                }
            } else {
                $emails = explode(',', notification_to());

                $data['mail_title'] = config('contact.message.new_contact');

                if (method_exists($model, 'transformEmail')) {
                    $data = array_merge($data, $model->transformEmail());
                }

                foreach ($emails as $email) {
                    Notification::route('mail', $email)
                        ->notify(new CommonNotification($data));
                }
            }

            // send customer
            $data = $model->data ?? [];
            if (method_exists($model, 'transformEmailDetails')) {
                $transformedData = $model->transformEmailDetails();
                if (is_array($transformedData)) {
                    $data = array_merge($data, $transformedData);
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
                    $data['mail_title'] = config('contact.message.success_form');
                }

                Notification::route('mail', $emailTo)
                    ->notify(new CommonNotification($data));
            }
        });
    }
}
