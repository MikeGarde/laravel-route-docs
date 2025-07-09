<?php

namespace Examples\Http\Controllers;

use RouteDocs\Attributes\delete;
use RouteDocs\Attributes\get;
use RouteDocs\Attributes\post;
use RouteDocs\Attributes\put;

class PostApiController
{
    #[get('/api/posts', name: 'posts.index')]
    public function index(): string
    {
        return 'Welcome to the Post API Controller!';
    }

    #[post('/api/posts', name: 'posts.store')]
    public function store(): string
    {
        return 'Store a new post';
    }

    #[get('/api/posts/{post}', name: 'posts.show')]
    public function show(int $post): string
    {
        return "Show post with ID: $post";
    }

    #[put('/api/posts/{post}', name: 'posts.update')]
    public function update(int $post): string
    {
        return "Update post with ID: $post";
    }

    #[delete('/api/posts/{post}', name: 'posts.destroy')]
    public function destroy(int $post): string
    {
        return "Delete post with ID: $post";
    }
}
