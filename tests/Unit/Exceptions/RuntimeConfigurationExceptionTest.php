<?php

use App\Exceptions\RuntimeConfigurationException;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

test('renders as json response', function () {
    $exception = new RuntimeConfigurationException('Test runtime configuration error');
    $request   = Request::create('/test', 'GET');

    $response = $exception->render($request);

    expect($response->getStatusCode())->toBe(500)
        ->and($response->getData(true))->toBe([
            'error'   => 'Runtime Configuration Error',
            'message' => 'Test runtime configuration error',
        ]);
});

test('includes custom message in json response', function () {
    $customMessage = 'Runtime not found';
    $exception     = new RuntimeConfigurationException($customMessage);
    $request       = Request::create('/test', 'GET');

    $response = $exception->render($request);

    $data = $response->getData(true);

    expect($data['message'])->toBe($customMessage)
        ->and($data['error'])->toBe('Runtime Configuration Error');
});
