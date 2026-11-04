<?php

namespace App\Models;

use App\Rules\Blackwords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use App\Models\BaseModel;
use App\Traits\Searchable;
use App\Traits\HasNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends BaseModel
{
    use HasFactory;
    use Searchable;
    use HasNotification;
    use SoftDeletes;

    const STATUS_NEW = 'NEW';
    const STATUS_CHECKING_CAD = 'CHECKING_CAD';
    const STATUS_QUOTED = 'QUOTED';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_IS_SPAM = 'IS_SPAM';

    // Aliases for backward compatibility
    const STATUS_RESPONSE = 'CHECKING_CAD';
    const STATUS_CONFIRMED = 'QUOTED';
    const STATUS_PROCESSED = 'PROCESSING';
    const STATUS_CLOSE = 'COMPLETED';

    const TYPE_CONTACT = 'CONTACT';

    public const STATUS_LIST = [
        self::STATUS_NEW => 'Mới nhận',
        self::STATUS_CHECKING_CAD => 'Đang kiểm tra file CAD',
        self::STATUS_QUOTED => 'Đã báo giá',
        self::STATUS_PROCESSING => 'Đang gia công/In',
        self::STATUS_COMPLETED => 'Hoàn tất',
        self::STATUS_IS_SPAM => 'Hủy/Spam',
    ];

    public const TYPE_LIST = [
        self::TYPE_CONTACT => 'Liên hệ',
    ];

    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'Mới nhận',
            'CHECKING_CAD', 'RESPONSE' => 'Đang kiểm tra file CAD',
            'QUOTED', 'CONFIRMED' => 'Đã báo giá',
            'PROCESSING', 'PROCESSED' => 'Đang gia công/In',
            'COMPLETED', 'CLOSE' => 'Hoàn tất',
            'IS_SPAM', 'CANCELLED' => 'Hủy/Spam',
            default => $this->status ?? '',
        };
    }

    public function getFormattedTypeAttribute(): string
    {
        switch ($this->type) {
            case 'CONTACT_FORM':
            case self::TYPE_CONTACT:
                return 'Liên hệ';
            default:
                return '';
        }
    }

    public $fillable = [
        'data',
        'type',
        'status',
        'estimated_price',
        'internal_notes',
        'images',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'images' => 'array',
        'estimated_price' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_created_at',
        'formatted_updated_at',
        'data_contact',
    ];

    protected $searchable = [
        'columns' => [
            'contacts.data' => 10,
            'contacts.id' => 5,
            'contacts.status' => 5,
        ],
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (request()->has('internal_notes')) {
                $model->internal_notes = request()->input('internal_notes');
            }
            if (request()->has('estimated_price')) {
                $model->estimated_price = request()->input('estimated_price');
            }

            // Bảo vệ 'data': Nếu model đã tồn tại và có data cũ, cấm ghi đè data thành null hoặc rỗng
            if ($model->exists) {
                $originalData = $model->getOriginal('data');
                if (!empty($originalData) && empty($model->data)) {
                    $model->data = is_string($originalData) ? json_decode($originalData, true) : $originalData;
                }
            }
        });

        static::creating(function ($model) {
            $model->ip_address = $model->setIpAddress();
            $model->user_agent = $model->setUserAgent();
            $model->request_url = $model->setRequestUrl();
            $model->status = $model->setStatus();

            if ($model->status == self::STATUS_IS_SPAM) {
                $model->deleted_at = now();
            }

            if (!is_array($model->data)) {
                $model->data = is_string($model->data) ? (json_decode($model->data, true) ?? []) : [];
            }

            if (is_array($model->data) && isset($model->data['note']) && !isset($model->data['customer_note'])) {
                $data = $model->data;
                $data['customer_note'] = $data['note'];
                $model->data = $data;
            }
        });
    }

    public function modelRules()
    {
        return [
            'store' => [
                'data' => 'exclude_unless:id,null|array|max:20',
            ],
        ];
    }

    private function setIpAddress()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }

    private function setUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Console/CLI';
    }

    private function setRequestUrl()
    {
        return request()->server->get('HTTP_REFERER') ?? 'Console/CLI';
    }

    private function setStatus()
    {
        return $this->isSpamContent() ? self::STATUS_IS_SPAM : self::STATUS_NEW;
    }

    private function isSpamContent()
    {
        $rules = ['*' => ['max:1000', new Blackwords]];
        return !Validator::make($this->data ?? [], $rules)->passes();
    }

    public function getDataContactAttribute()
    {
        if (empty($this->attributes['data'])) {
            return (object)[];
        }
        return is_string($this->attributes['data']) ? json_decode($this->attributes['data']) : (object)$this->attributes['data'];
    }

    public function getConfigTypeKey(): string
    {
        if ($this->type === self::TYPE_CONTACT || $this->type === 'CONTACT') {
            return 'CONTACT_FORM';
        }
        return (string)($this->type ?? '');
    }

    public function transformEmail()
    {
        $data = ['Form' => $this->formatted_type];
        $key = $this->getConfigTypeKey();
        $columns = config('contact.types.' . $key . '.columns') ?? config('contact.types.' . $this->type . '.columns') ?? [];
        if (is_iterable($columns)) {
            foreach ($columns as $column) {
                $data[$column] = $this->data[$column] ?? null;
            }
        }

        $data['url'] = $this->getUrl();
        return $data;
    }

    public function transformEmailDetails()
    {
        $data = ['Form' => $this->formatted_type];

        $key = $this->getConfigTypeKey();
        $columns = config('contact.types.' . $key . '.all_columns') ?? config('contact.types.' . $this->type . '.all_columns');

        if (empty($columns) || !is_array($columns)) {
            $data = array_merge($data, $this->data ?? []);
        } else {
            foreach ($columns as $key => $column) {
                if (is_array($column) && isset($column['route']['name'])) {
                    $params = [];
                    foreach ($column['route']['params'] as $param) {
                        $params[$param] = $this->data[$key][$param] ?? null;
                    }

                    $routeLocale = config('app.locale');

                    foreach (config('app.locales') as $locale) {
                        if (strpos($this->request_url, '/' . $locale . '/')) {
                            $routeLocale = $locale;
                        }
                    }

                    $data[$column['column']] = route($routeLocale . '.' . $column['route']['name'], $params);
                } else if ($column == 'File CV' && is_array($this->data[$column] ?? null)) {
                    $files = [];
                    foreach ($this->data[$column] as $index => $file) {
                        $files['CV ' . ($index + 1)] = env('APP_URL') . $file;
                    }
                    $data[$column] = $files;
                } else if ($column === 'Service' && is_array($this->data[$column] ?? null)) {
                    $data['Dịch vụ'] = $this->data['Service']['title'] ?? '';
                } else {
                    $data[$column] = $this->data[$column] ?? null;
                }
            }
        }

        // Metadata for all templates
        $data['OrderID'] = '#' . $this->id;
        $data['CreatedAt'] = $this->created_at->format('H:i d/m/Y');

        return $data;
    }

    public function statusLogs()
    {
        return $this->hasMany(ContactStatusLog::class, 'contact_id')->latest();
    }

    public function getUrl()
    {
        $key = $this->getConfigTypeKey();
        $route = config('contact.types.' . $key . '.route') ?? config('contact.types.' . $this->type . '.route', 'contacts');
        return route(current_locale() . '.admin.' . $route . '.form', ['id' => $this->id]);
    }
}
