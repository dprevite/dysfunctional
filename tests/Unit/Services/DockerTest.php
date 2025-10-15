<?php

use App\Services\Docker;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

uses(TestCase::class);

test('processes returns all running docker containers', function () {
    $dockerOutput = json_encode(['ID' => 'abc123', 'Names' => 'container1', 'Image' => 'nginx']) . "\n" .
                   json_encode(['ID' => 'def456', 'Names' => 'container2', 'Image' => 'redis']);

    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result($dockerOutput),
    ]);

    $docker = new Docker;
    $processes = $docker->processes();

    expect($processes)->toHaveCount(2)
        ->and($processes[0])->toBe(['ID' => 'abc123', 'Names' => 'container1', 'Image' => 'nginx'])
        ->and($processes[1])->toBe(['ID' => 'def456', 'Names' => 'container2', 'Image' => 'redis']);
});

test('processes returns empty array when no containers running', function () {
    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result(''),
    ]);

    $docker = new Docker;
    $processes = $docker->processes();

    expect($processes)->toBeEmpty();
});

test('processes returns empty array when docker command fails', function () {
    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result('', 1),
    ]);

    $docker = new Docker;
    $processes = $docker->processes();

    expect($processes)->toBeEmpty();
});

test('isProcessRunning returns true when process with matching name exists', function () {
    $dockerOutput = json_encode(['ID' => 'abc123', 'Names' => 'dysfunctional-app', 'Image' => 'nginx']) . "\n" .
                   json_encode(['ID' => 'def456', 'Names' => 'redis-cache', 'Image' => 'redis']);

    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result($dockerOutput),
    ]);

    $docker = new Docker;

    expect($docker->isProcessRunning('dysfunctional'))->toBeTrue()
        ->and($docker->isProcessRunning('redis'))->toBeTrue();
});

test('isProcessRunning returns false when no matching process exists', function () {
    $dockerOutput = json_encode(['ID' => 'abc123', 'Names' => 'container1', 'Image' => 'nginx']);

    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result($dockerOutput),
    ]);

    $docker = new Docker;

    expect($docker->isProcessRunning('postgres'))->toBeFalse();
});

test('isProcessRunning returns false when no containers running', function () {
    Process::fake([
        'docker ps --format "{{json .}}"' => Process::result(''),
    ]);

    $docker = new Docker;

    expect($docker->isProcessRunning('any-name'))->toBeFalse();
});

test('getImage returns image data when image exists', function () {
    $imageData = [
        [
            'Id' => 'sha256:abc123',
            'RepoTags' => ['nginx:latest'],
            'Size' => 142000000,
        ],
    ];

    Process::fake([
        'docker image inspect nginx:latest' => Process::result(json_encode($imageData)),
    ]);

    $docker = new Docker;
    $image = $docker->getImage('nginx:latest');

    expect($image)->toBe($imageData[0]);
});

test('getImage returns null when image does not exist', function () {
    Process::fake([
        'docker image inspect nonexistent-image' => Process::result('', 1),
    ]);

    $docker = new Docker;
    $image = $docker->getImage('nonexistent-image');

    expect($image)->toBeNull();
});

test('getImage returns null when output is empty', function () {
    Process::fake([
        'docker image inspect empty-image' => Process::result(''),
    ]);

    $docker = new Docker;
    $image = $docker->getImage('empty-image');

    expect($image)->toBeNull();
});

test('getContainer returns container data when container exists', function () {
    $containerData = [
        [
            'Id' => 'abc123def456',
            'Name' => '/my-container',
            'State' => [
                'Status' => 'running',
                'Running' => true,
            ],
        ],
    ];

    Process::fake([
        'docker container inspect my-container' => Process::result(json_encode($containerData)),
    ]);

    $docker = new Docker;
    $container = $docker->getContainer('my-container');

    expect($container)->toBe($containerData[0]);
});

test('getContainer returns null when container does not exist', function () {
    Process::fake([
        'docker container inspect nonexistent-container' => Process::result('', 1),
    ]);

    $docker = new Docker;
    $container = $docker->getContainer('nonexistent-container');

    expect($container)->toBeNull();
});

test('getContainer returns null when output is empty', function () {
    Process::fake([
        'docker container inspect empty-container' => Process::result(''),
    ]);

    $docker = new Docker;
    $container = $docker->getContainer('empty-container');

    expect($container)->toBeNull();
});

test('buildImage uses correct tag from runtime config', function () {
    $runtimeConfig = new \App\Data\Config\RuntimeConfig(
        path: 'serverless/php-8.4',
        file: storage_path('config/runtimes/serverless/php-8.4/runtime.yml'),
        language: 'PHP',
        version: '8.4',
        platform: 'Linux'
    );

    expect($runtimeConfig->getDockerImageTag())->toBe('dysfunctional-linux-php-84:latest');
});

test('buildImage returns process result', function () {
    $runtimeConfig = new \App\Data\Config\RuntimeConfig(
        path: 'serverless/php-8.4',
        file: storage_path('config/runtimes/serverless/php-8.4/runtime.yml'),
        language: 'PHP',
        version: '8.4',
        platform: 'Linux'
    );

    Process::fake([
        '*' => Process::result('Successfully built image'),
    ]);

    $docker = new Docker;
    $result = $docker->buildImage($runtimeConfig);

    expect($result)->toBeInstanceOf(\Illuminate\Contracts\Process\ProcessResult::class)
        ->and($result->successful())->toBeTrue()
        ->and(trim($result->output()))->toBe('Successfully built image');
});
