<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Actions\Blog\ListPosts;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Site/Blog/Index', [
            'posts' => ListPosts::execute(),
            'seo' => [
                'title' => 'Blog',
                'description' => 'Notes on short links, click analytics and running them yourself.',
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $post = ListPosts::find($slug);

        abort_if($post === null, HttpResponse::HTTP_NOT_FOUND);

        return Inertia::render('Site/Blog/Show', [
            'post' => $post,
            'seo' => [
                'title' => $post['title'],
                'description' => $post['description'],
                'image' => $post['image'],
            ],
        ]);
    }
}
