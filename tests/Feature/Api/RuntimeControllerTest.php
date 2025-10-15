<?php

use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('can get all runtimes', function () {
    $runtimes = [
        ['language' => 'PHP', 'version' => '8.4', 'platform' => 'linux'],
        ['language' => 'Node', 'version' => '20.0', 'platform' => 'linux'],
    ];

    Cache::put('config.runtimes', $runtimes, now()->addMinutes(60));

    $response = $this->getJson('/api/runtimes');

    $response->assertSuccessful()
        ->assertJson([
            'data' => $runtimes,
        ]);
});

test('returns empty array when no runtimes exist', function () {
    $response = $this->getJson('/api/runtimes');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [],
        ]);
});

test('can get a specific runtime by language', function () {
    $runtimes = [
        ['language' => 'PHP', 'version' => '8.4', 'platform' => 'linux'],
        ['language' => 'Node', 'version' => '20.0', 'platform' => 'linux'],
    ];

    Cache::put('config.runtimes', $runtimes, now()->addMinutes(60));

    $response = $this->getJson('/api/runtimes/PHP');

    $response->assertSuccessful()
        ->assertJson([
            'data' => $runtimes[0],
        ]);
});

test('returns 404 when runtime not found', function () {
    $runtimes = [
        ['language' => 'PHP', 'version' => '8.4', 'platform' => 'linux'],
    ];

    Cache::put('config.runtimes', $runtimes, now()->addMinutes(60));

    $response = $this->getJson('/api/runtimes/Python');

    $response->assertNotFound()
        ->assertJson([
            'message' => 'Runtime not found',
        ]);
});
