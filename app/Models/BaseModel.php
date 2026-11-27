<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ResponseCache;
use App\Traits\HasRichText;

/**
 * Class BaseModel
 * @package App\Models
 */
class BaseModel extends Model
{
    use ResponseCache;
    use HasRichText;

    public function getFormattedUpdatedAtAttribute(): string
    {
        return to_date($this->attributes['updated_at'], 'd/m/Y');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return to_date($this->attributes['created_at'], 'd/m/Y');
    }

    public function getCurrentUrlAttribute()
    {
        $locale = current_locale();
        $defaultLocale = config('app.locale_default_frontend') ?: config('app.fallback_locale') ?: config('app.locale', 'vi');

        $url = $this->url[strtoupper($locale)] ?? null;

        if (config('app.use_default_locale', true) && $locale != $defaultLocale && empty($url)) {
            $defaultUrl = $this->url[strtoupper($defaultLocale)] ?? null;

            if (empty($defaultUrl) || !is_string($defaultUrl)) {
                return null;
            }

            $appUrl = rtrim((string) config('app.url'), '/');

            if ($appUrl !== '' && str_starts_with($defaultUrl, $appUrl)) {
                $suffix = substr($defaultUrl, strlen($appUrl));
                if ($suffix === '' || str_starts_with($suffix, '/')) {
                    return $appUrl . '/' . $locale . $suffix;
                }
            }

            if (str_starts_with($defaultUrl, '/') && !str_starts_with($defaultUrl, '//')) {
                return $defaultUrl === '/' ? '/' . $locale : '/' . $locale . $defaultUrl;
            }

            $parsed = parse_url($defaultUrl);
            if ($parsed !== false && !empty($parsed['scheme']) && !empty($parsed['host'])) {
                $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
                $path = $parsed['path'] ?? '';
                $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
                $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

                return $parsed['scheme'] . '://' . $parsed['host'] . $port . '/' . $locale . $path . $query . $fragment;
            }

            return $defaultUrl;
        }

        return $url;
    }
}
