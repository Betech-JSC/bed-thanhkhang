<?php

namespace JamstackVietnam\Core\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasRoles;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type',
        'status',

        'google2fa_secret',
        'google2fa_ts'
    ];

    protected $appends = [
        'verified', 'role_names', 'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $rules = [
        'email' => 'string',
        'password' => 'confirmed|nullable|min:8|max:255'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->google2fa_secret = generate_code(16, 'uppercase');
        });

        static::saving(function ($model) {
            if (request()->route()?->getName() !== 'admin.admins.store') return;

            if ($password = request()->input('password')) {
                $model->password = Hash::make($password);
            } else {
                unset($model->password);
            }

            // Clean up existing soft-deleted records with the same email or phone
            // to prevent unique constraint conflicts.
            if ($model->email) {
                $existingEmail = static::onlyTrashed()
                    ->where('email', $model->email)
                    ->where('id', '<>', $model->id)
                    ->first();
                if ($existingEmail) {
                    $timestamp = time();
                    $existingEmail->email = $existingEmail->email . '.deleted.' . $timestamp;
                    if ($existingEmail->phone) {
                        $existingEmail->phone = $existingEmail->phone . '.deleted.' . $timestamp;
                    }
                    $existingEmail->saveQuietly();
                }
            }

            if ($model->phone) {
                $existingPhone = static::onlyTrashed()
                    ->where('phone', $model->phone)
                    ->where('id', '<>', $model->id)
                    ->first();
                if ($existingPhone) {
                    $timestamp = time();
                    $existingPhone->email = $existingPhone->email . '.deleted.' . $timestamp;
                    $existingPhone->phone = $existingPhone->phone . '.deleted.' . $timestamp;
                    $existingPhone->saveQuietly();
                }
            }
        });

        static::saved(function ($model) {
            if (request()->route()?->getName() !== 'admin.admins.store') return;

            $role = Role::find(request()->input('role_id'));
            $model->syncRoles([$role]);
        });

        static::deleting(function ($model) {
            $timestamp = time();
            $model->email = $model->email . '.deleted.' . $timestamp;
            if ($model->phone) {
                $model->phone = $model->phone . '.deleted.' . $timestamp;
            }
            $model->saveQuietly();
        });

        static::restoring(function ($model) {
            $cleanEmail = preg_replace('/\.deleted\.\d+$/', '', $model->email);

            if (static::where('email', $cleanEmail)->exists()) {
                throw new \Exception('Email này đã được sử dụng bởi một tài khoản đang hoạt động khác.');
            }

            $model->email = $cleanEmail;
            if ($model->phone) {
                $model->phone = preg_replace('/\.deleted\.\d+$/', '', $model->phone);
            }
        });
    }

    public function getRoleNamesAttribute()
    {
        return $this->getRoleNames();
    }

    public function getRoleIdAttribute()
    {
        return $this->roles->first()?->id;
    }

    public function getVerifiedAttribute()
    {
        return $this->google2fa_ts !== 0;
    }

    public function scopeVerified($query)
    {
        $query->where('google2fa_ts', '<>', 0);
    }

    public function transformCurrentAdmin()
    {
        if (current_admin_id() === $this->id) {
            $this->name .= ' (Đang đăng nhập)';
        }
        return $this;
    }
}
