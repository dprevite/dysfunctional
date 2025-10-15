<?php

use App\Actions\Scan\FunctionScanner;
use App\Actions\Scan\RuntimeScanner;
use App\Services\Config;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Cache::flush();
});

test('functions returns cached data when available', function () {
    $expected = [
        ['name' => 'test-function', 'runtime' => 'php'],
    ];

    Cache::put('config.functions', $expected, now()->addMinutes(60));

    $functionScanner = $this->mock(FunctionScanner::class);
    $functionScanner->shouldNotReceive('scan');

    $runtimeScanner = $this->mock(RuntimeScanner::class);

    $config = new Config($functionScanner, $runtimeScanner);

    expect($config->functions())->toBe($expected);
});

test('functions scans and caches when cache is empty', function () {
    $expected = [
        ['name' => 'test-function', 'runtime' => 'php'],
    ];

    $functionScanner = $this->mock(FunctionScanner::class);
    $functionScanner->shouldReceive('scan')
        ->once()
        ->andReturn($expected);

    $runtimeScanner = $this->mock(RuntimeScanner::class);

    $config = new Config($functionScanner, $runtimeScanner);

    $result = $config->functions();

    expect($result)->toBe($expected)
        ->and(Cache::has('config.functions'))->toBeTrue()
        ->and(Cache::get('config.functions'))->toBe($expected);
});

test('functions uses cache on subsequent calls', function () {
    $expected = [
        ['name' => 'test-function', 'runtime' => 'php'],
    ];

    $functionScanner = $this->mock(FunctionScanner::class);
    $functionScanner->shouldReceive('scan')
        ->once()
        ->andReturn($expected);

    $runtimeScanner = $this->mock(RuntimeScanner::class);

    $config = new Config($functionScanner, $runtimeScanner);

    $firstCall = $config->functions();
    $secondCall = $config->functions();

    expect($firstCall)->toBe($expected)
        ->and($secondCall)->toBe($expected);
});

test('runtimes returns cached data when available', function () {
    $expected = [
        ['language' => 'PHP', 'version' => '8.4'],
    ];

    Cache::put('config.runtimes', $expected, now()->addMinutes(60));

    $functionScanner = $this->mock(FunctionScanner::class);

    $runtimeScanner = $this->mock(RuntimeScanner::class);
    $runtimeScanner->shouldNotReceive('scan');

    $config = new Config($functionScanner, $runtimeScanner);

    expect($config->runtimes())->toBe($expected);
});

test('runtimes scans and caches when cache is empty', function () {
    $expected = [
        ['language' => 'PHP', 'version' => '8.4'],
    ];

    $functionScanner = $this->mock(FunctionScanner::class);

    $runtimeScanner = $this->mock(RuntimeScanner::class);
    $runtimeScanner->shouldReceive('scan')
        ->once()
        ->andReturn($expected);

    $config = new Config($functionScanner, $runtimeScanner);

    $result = $config->runtimes();

    expect($result)->toBe($expected)
        ->and(Cache::has('config.runtimes'))->toBeTrue()
        ->and(Cache::get('config.runtimes'))->toBe($expected);
});

test('runtimes uses cache on subsequent calls', function () {
    $expected = [
        ['language' => 'PHP', 'version' => '8.4'],
    ];

    $functionScanner = $this->mock(FunctionScanner::class);

    $runtimeScanner = $this->mock(RuntimeScanner::class);
    $runtimeScanner->shouldReceive('scan')
        ->once()
        ->andReturn($expected);

    $config = new Config($functionScanner, $runtimeScanner);

    $firstCall = $config->runtimes();
    $secondCall = $config->runtimes();

    expect($firstCall)->toBe($expected)
        ->and($secondCall)->toBe($expected);
});
