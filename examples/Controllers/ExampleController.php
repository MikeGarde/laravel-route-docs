<?php

namespace Examples\Http\Controllers;

use RouteDocs\Attributes\get;
use RouteDocs\Attributes\post;

class ExampleController
{
    #[get('/ping', name: 'ping.get')]
    public function ping(): string
    {
        return 'pong';
    }

    #[get('/status', name: 'status.get')]
    public function status(int $element = null): array
    {
        return ['status' => 'ok'];
    }

    #[post('/status/{element}', name: 'status.post')]
    public function updateStatus(int $element): array
    {
        return ['status' => 'updated'];
    }

    #[get('/')]
    #[get('/home', name: 'home.index')]
    #[post('/home', name: 'home.post')]
    public function legacyHome(): string
    {
        // Sometimes you inherit insane legacy code
        return 'Welcome to the Example Controller!';
    }
}
