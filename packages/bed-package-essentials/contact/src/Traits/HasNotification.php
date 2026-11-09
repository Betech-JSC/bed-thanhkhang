<?php

namespace JamstackVietnam\Contact\Traits;

use Illuminate\Support\Facades\Notification;
use JamstackVietnam\Contact\Notifications\CommonNotification;
use JamstackVietnam\Contact\Models\Contact;

trait HasNotification
{
    public static function bootHasNotification()
    {
        static::created(function ($model) {
            if (request()->route() === null || !config('contact.send_email_default', true)) return;

            try {
                if ($model->status === Contact::STATUS_IS_SPAM) {
                    $emails = array_filter(array_map('trim', explode(',', config('contact.mail_spam', 'khapcn.flamedia@gmail.com'))));

                    $data['mail_title'] = config('contact.message.new_spam', 'Thông báo nhận được Spam');
                    $data = array_merge($data, $model->data ?? []);

                    $route = config('contact.types.' . $model->type . '.route', 'contacts');
                    $data['url'] = route(current_locale() . '.admin.' . $route . '.form', [ 'id' => $model->id ]);

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
                \Log::error('JamstackVietnam HasNotification admin email failed: ' . $e->getMessage(), [
                    'contact_id' => $model->id ?? null,
                    'exception'  => $e,
                ]);
            }

            // send customer
            try {
                if (method_exists($model, 'transformEmailDetails')) {
                    $data = $model->transformEmailDetails();
                } else {
                    $data = $model->data ?? [];
                }

                $data['mail_title'] = config('contact.message.success_form', 'Chúc mừng bạn gửi form thành công');
                $emailTo = $data['Email'] ?? $data['email'] ?? null;
                if (!empty($emailTo)) {
                    Notification::route('mail', $emailTo)
                        ->notify(new CommonNotification($data));
                }
            } catch (\Throwable $e) {
                \Log::error('JamstackVietnam HasNotification customer email failed: ' . $e->getMessage(), [
                    'contact_id' => $model->id ?? null,
                    'exception'  => $e,
                ]);
            }
        });
    }
}
