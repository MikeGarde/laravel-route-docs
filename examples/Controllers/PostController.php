<?php

namespace Examples\Http\Controllers;

use RouteDocs\Attributes\delete;
use RouteDocs\Attributes\get;
use RouteDocs\Attributes\post;
use RouteDocs\Attributes\put;

class PostController
{
    #[get('/posts', name: 'posts.index')]
    public function index(): string
    {
        return 'Welcome to the Post Controller!';
    }

    #[post('/posts', name: 'posts.store')]
    public function store(): string
    {
        return 'Store a new post';
    }

    #[get('/posts/create', name: 'posts.create')]
    public function create(): string
    {
        return 'Create a new post';
    }

    #[get('/posts/{post}', name: 'posts.show')]
    public function show(int $post): string
    {
        return "Show post with ID: $post";
    }

    #[get('/posts/{post}/edit', name: 'posts.edit')]
    public function edit(int $post): string
    {
        return "Edit post with ID: $post";
    }

    #[put('/posts/{post}', name: 'posts.update')]
    public function update(int $post): string
    {
        return "Update post with ID: $post";
    }

    #[delete('/posts/{post}', name: 'posts.destroy')]
    public function destroy(int $post): string
    {
        return "Delete post with ID: $post";
    }
}
