<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactStatusLog extends Model
{
    use HasFactory;

    protected $table = 'contact_status_logs';

    protected $fillable = [
        'contact_id',
        'old_status',
        'new_status',
        'changed_by',
        'admin_name',
        'reason',
        'note',
        'ip_address',
        'user_agent',
    ];

    protected $appends = [
        'formatted_created_at',
        'old_status_label',
        'new_status_label',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : '';
    }

    public function getOldStatusLabelAttribute(): string
    {
        return $this->formatStatusLabel($this->old_status);
    }

    public function getNewStatusLabelAttribute(): string
    {
        return $this->formatStatusLabel($this->new_status);
    }

    private function formatStatusLabel(?string $status): string
    {
        if (empty($status)) {
            return 'Khởi tạo';
        }

        $labels = [
            Contact::STATUS_NEW       => 'Mới đăng ký',
            Contact::STATUS_RESPONSE  => 'Đã liên hệ',
            Contact::STATUS_CONFIRMED => 'Đã xác nhận',
            Contact::STATUS_PROCESSED => 'Đang học',
            'COMPLETED'               => 'Đã tốt nghiệp',
            Contact::STATUS_CLOSE     => 'Đã hủy',
            Contact::STATUS_IS_SPAM   => 'Spam',
        ];

        return $labels[$status] ?? $status;
    }
}
