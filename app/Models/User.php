<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Searchable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Searchable;

    protected $searchable = [
        'columns' => [
            'users.name' => 10,
            'users.email' => 10,
            'users.phone' => 10,
        ],
    ];

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_DELETED = 'DELETED';
    public const STATUS_EDITING = 'EDITING';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Đang hoạt động',
        self::STATUS_INACTIVE => 'Tắt',
        self::STATUS_PENDING  => 'Chờ duyệt',
        self::STATUS_APPROVED => 'Đã duyệt',
        self::STATUS_EXPIRED  => 'Hết hạn',
        self::STATUS_REJECTED => 'Bị từ chối',
        self::STATUS_DELETED  => 'Đã xóa',
        self::STATUS_EDITING  => 'Đang chỉnh sửa',
    ];

    public const ROLE_ADMIN     = 'admin';
    public const ROLE_CUSTOMER  = 'customer';
    public const ROLE_OTHER     = 'other';

    public const ROLE_LIST = [
        self::ROLE_ADMIN     => 'Quản trị viên',
        self::ROLE_CUSTOMER  => 'Khách hàng',
        self::ROLE_OTHER     => 'Khác',
    ];

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'phone',
        'zalo',
        'status',
        'role',
        'address',
        'note',
        'banner',
        'avatar',
        'password',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'banner' => 'json',
        'avatar' => 'json',
    ];

    public $appends = ['formatted_status', 'formatted_role'];

    protected static function booted()
    {
        static::saved(function (self $model) {
            $phone = $model->phone;
            if (!empty($phone)) {
                \Illuminate\Support\Facades\Cache::forget('otp_' . $phone);
            }
        });

        static::deleted(function (self $model) {
            $phone = $model->phone;
            if (!empty($phone)) {
                \Illuminate\Support\Facades\Cache::forget('otp_' . $phone);
            }
        });
    }

    public function wishlistedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_user_wishlist')
            ->withTimestamps();
    }

    public function getFormattedStatusAttribute()
    {
        return self::STATUS_LIST[$this->status] ?? 'Không xác định';
    }

    public function getFormattedRoleAttribute()
    {
        return self::ROLE_LIST[$this->role] ?? 'Người dùng';
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_id');
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'email' => $this->email,
            'zalo' => $this->zalo,
            'address' => $this->address,
            'note' => $this->note,
            'avatar' => $this->getImageDetail($this->avatar),
            'formatted_status' => $this->formatted_status,
            'role' => $this->role,
            'formatted_role' => $this->formatted_role,
        ];
    }

    public function transformDetails()
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'phone'                   => $this->phone,
            'created_at'              => $this->created_at,
            'status'                  => $this->status,
            'zalo'                    => $this->zalo,
            'email'                   => $this->email,
            'address'                 => $this->address,
            'note'                    => $this->note,
            'avatar'                  => $this->getImageDetail($this->avatar),
            'banner'                  => $this->getImageDetail($this->banner),
            'formatted_status'        => $this->formatted_status,
            'role'                    => $this->role,
            'formatted_role'          => $this->formatted_role,
            'last_login_at'           => $this->last_login_at?->toIso8601String(),
        ];
    }

    public function rules()
    {
        return [
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'status' => 'required|string|in:ACTIVE,INACTIVE',
            'role'   => 'nullable|string|in:admin,customer,other',
        ];
    }

    public function getImageDetail($image)
    {
        return [
            'url' => isset($image['path']) ? static_url($image['path']) : null,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }
}
