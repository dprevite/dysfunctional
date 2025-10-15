<?php

use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('can get all functions', function () {
    $functions = [
        ['name' => 'test-function-1', 'runtime' => 'php', 'route' => '/test1'],
        ['name' => 'test-function-2', 'runtime' => 'node', 'route' => '/test2'],
    ];

    Cache::put('config.functions', $functions, now()->addMinutes(60));

    $response = $this->getJson('/api/functions');

    $response->assertSuccessful()
        ->assertJson([
            'data' => $functions,
        ]);
});

test('returns empty array when no functions exist', function () {
    $response = $this->getJson('/api/functions');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [],
        ]);
});

test('can get a specific function by name', function () {
    $functions = [
        ['name' => 'test-function-1', 'runtime' => 'php', 'route' => '/test1'],
        ['name' => 'test-function-2', 'runtime' => 'node', 'route' => '/test2'],
    ];

    Cache::put('config.functions', $functions, now()->addMinutes(60));

    $response = $this->getJson('/api/functions/test-function-1');

    $response->assertSuccessful()
        ->assertJson([
            'data' => $functions[0],
        ]);
});

test('returns 404 when function not found', function () {
    $functions = [
        ['name' => 'test-function-1', 'runtime' => 'php', 'route' => '/test1'],
    ];

    Cache::put('config.functions', $functions, now()->addMinutes(60));

    $response = $this->getJson('/api/functions/non-existent-function');

    $response->assertNotFound()
        ->assertJson([
            'message' => 'Function not found',
        ]);
});
