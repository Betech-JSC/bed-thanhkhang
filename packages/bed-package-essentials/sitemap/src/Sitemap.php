<?php

namespace JamstackVietnam\Sitemap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

class Sitemap
{
    public array $tags = [];

    const CHANGE_FREQUENCY_DAILY = 'daily';
    const PRIORITY = 0.8;

    public static function create(): static
    {
        return new static();
    }

    public function addStaticRoutes(): static
    {
        // 1. Always add homepage with highest priority 1.0
        $this->tags[] = [
            'url' => url('/'),
            'name' => 'home',
            'lastModificationDate' => now()->toAtomString(),
            'changeFrequency' => self::CHANGE_FREQUENCY_DAILY,
            'priority' => 1.0,
        ];

        $excludedPatterns = [
            'sitemap.xml',
            'robots.txt',
            'error',
            'search',
            'tim-kiem',
            'api/search',
            'search-v2',
            'ho-so-ca-nhan',
            'about',
            'about-us',
            've-chung-toi',
            'dang-nhap',
            'dang-ky',
            'quen-mat-khau',
            'dat-lai-mat-khau',
            'dashboard',
            'demo',
            'test',
        ];

        foreach (Route::getRoutes() as $route) {
            if ($route->getName() === 'dynamic-redirect') continue;

            $action = $route->getAction();
            $controller = $action['controller'] ?? '';
            if (str_contains($controller, 'RedirectController')) continue;

            if (isset($action['middleware']) && in_array('GET', $route->methods())) {
                $middleware = $action['middleware'];
                $uri = trim($route->uri, '/');
                if (is_array($middleware)) {
                    if (
                        in_array('frontend', $middleware) &&
                        !str_contains($uri, '{') &&
                        !str_contains($uri, '}') &&
                        $uri !== ''
                    ) {
                        $isExcluded = false;
                        foreach ($excludedPatterns as $pattern) {
                            if ($uri === $pattern || str_starts_with($uri, $pattern . '/') || str_ends_with($uri, '/' . $pattern)) {
                                $isExcluded = true;
                                break;
                            }
                        }

                        if (!$isExcluded) {
                            $this->add(url($uri));
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function add(string | iterable | Model $tag, $name = null): static
    {
        if (is_iterable($tag)) {
            foreach ($tag as $item) {
                $this->add($item);
            }

            return $this;
        }

        if (is_string($tag)) {
            $tag = $this->createUrl($tag, $name);
        }

        if (!is_array($tag)) {
            $tag = $this->transformUrl($tag);
        }

        if (!in_array($tag, $this->tags)) {
            if (is_array($tag) && count($tag) && is_array(head($tag))) {
                $this->tags = array_merge($this->tags, $tag);
            } else {
                $this->tags[] = $tag;
            }
        }

        return $this;
    }

    public function render()
    {
        $items = collect($this->tags)
            ->whereNotNull('url')
            ->unique('url')
            ->filter()
            ->sortByDesc('priority')
            ->values();

        return response()
            ->view('sitemap::sitemap', ['items' => $items])
            ->header('Content-Type', 'text/xml');
    }

    private function transformUrl($item)
    {
        if (is_array($item->url)) {
            $urls = [];
            foreach ($item->url as $url) {
                $newItem = (object) $item->toArray();
                $newItem->url = $url;
                $urls[] = $this->transformUrl($newItem);
            }
            return $urls;
        } else {
            return [
                'url' => $item->url,
                'lastModificationDate' => Carbon::create($item->created_at)->toAtomString(),
                'changeFrequency' => self::CHANGE_FREQUENCY_DAILY,
                'priority' => $item->priority ?? self::PRIORITY,
            ];
        }
    }

    private function createUrl(string $url, $name = null)
    {
        return [
            'url' => $url,
            'name' => $name,
            'lastModificationDate' => now()->toAtomString(),
            'changeFrequency' => self::CHANGE_FREQUENCY_DAILY,
            'priority' => self::PRIORITY
        ];
    }
}
