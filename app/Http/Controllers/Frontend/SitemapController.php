<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use JamstackVietnam\Sitemap\Sitemap;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Models\Policy\Policy;

class SitemapController extends Controller
{
    public function index()
    {
        $data = Sitemap::create()
            ->addStaticRoutes()
            ->add(Post::active()->get())
            ->add(PostCategory::active()->get())
            ->add(Product::active()->get())
            ->add(Policy::active()->get())
            ->render();
        return $data;
    }
}
