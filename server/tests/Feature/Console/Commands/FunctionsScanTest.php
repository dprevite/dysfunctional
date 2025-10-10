<?php

use Illuminate\Support\Facades\File;

describe('FunctionsScan Command', function () {
    beforeEach(function () {
        // Create a temporary functions directory for testing
        $this->testFunctionsPath = storage_path('framework/testing/functions');
        File::ensureDirectoryExists($this->testFunctionsPath);
    });

    afterEach(function () {
        // Clean up the temporary functions directory
        File::deleteDirectory($this->testFunctionsPath);
    });

    it('displays warning when no function files are found', function () {
        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath])
            ->expectsOutput('No function.yml files found in the functions directory.')
            ->assertSuccessful();
    });

    it('scans and displays single function in table format', function () {
        // Create a test function.yml file
        $functionPath = $this->testFunctionsPath.'/test-function';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: TestFunction
  description: A test function
  route: /test
  method: GET
  runtime: php:8.4
  entrypoint: index.php
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath])
            ->expectsOutputToContain('TestFunction')
            ->assertSuccessful();
    });

    it('scans and displays multiple functions', function () {
        // Create first function
        $function1Path = $this->testFunctionsPath.'/function-one';
        File::ensureDirectoryExists($function1Path);
        File::put($function1Path.'/function.yml', <<<'YAML'
function:
  name: Function One
  description: First function
  route: /one
  method: GET
  runtime: php:8.4
  entrypoint: index.php
YAML);

        // Create second function
        $function2Path = $this->testFunctionsPath.'/function-two';
        File::ensureDirectoryExists($function2Path);
        File::put($function2Path.'/function.yml', <<<'YAML'
function:
  name: Function Two
  description: Second function
  route: /two
  method: POST
  runtime: node:20
  entrypoint: main.js
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath])
            ->expectsOutputToContain('Found 2 function(s):')
            ->expectsOutputToContain('Function One')
            ->expectsOutputToContain('Function Two')
            ->assertSuccessful();
    });

    it('validates function definitions when --validate option is provided', function () {
        $functionPath = $this->testFunctionsPath.'/valid-function';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: ValidFunction
  description: A valid function
  route: /valid
  method: GET
  runtime: php:8.4
  entrypoint: index.php
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
            ->expectsOutputToContain('✓ Validation complete')
            ->assertSuccessful();
    });

    it('displays validation errors for missing function section', function () {
        $functionPath = $this->testFunctionsPath.'/invalid-function';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
docker:
  volumes: []
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
            ->expectsOutputToContain("Missing 'function' section")
            ->assertSuccessful();
    });

    it('displays validation errors for missing required fields', function () {
        $functionPath = $this->testFunctionsPath.'/incomplete-function';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: IncompleteFunction
  route: /incomplete
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
            ->expectsOutputToContain('Missing required field: function.description')
            ->expectsOutputToContain('Missing required field: function.method')
            ->expectsOutputToContain('Missing required field: function.runtime')
            ->expectsOutputToContain('Missing required field: function.entrypoint')
            ->assertSuccessful();
    });

    it('displays validation error when route does not start with slash', function () {
        $functionPath = $this->testFunctionsPath.'/bad-route';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: BadRoute
  description: Function with bad route
  route: test
  method: GET
  runtime: php:8.4
  entrypoint: index.php
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
            ->expectsOutputToContain("Route must start with '/'")
            ->assertSuccessful();
    });

    it('displays validation error for invalid HTTP method', function () {
        $functionPath = $this->testFunctionsPath.'/bad-method';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: BadMethod
  description: Function with invalid HTTP method
  route: /bad
  method: INVALID
  runtime: php:8.4
  entrypoint: index.php
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
            ->expectsOutputToContain('Invalid HTTP method: INVALID')
            ->assertSuccessful();
    });

    it('handles YAML parsing errors gracefully', function () {
        $functionPath = $this->testFunctionsPath.'/invalid-yaml';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', 'invalid: yaml: syntax: [[[');

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath])
            ->expectsOutputToContain('Error processing')
            ->assertSuccessful();
    });

    it('outputs JSON format when --json option is provided', function () {
        $functionPath = $this->testFunctionsPath.'/test-function';
        File::ensureDirectoryExists($functionPath);
        File::put($functionPath.'/function.yml', <<<'YAML'
function:
  name: TestFunction
  description: A test function
  route: /test
  method: GET
  runtime: php:8.4
  entrypoint: index.php
YAML);

        $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--json' => true])
            ->expectsOutputToContain('TestFunction')
            ->assertSuccessful();
    });

    it('handles all valid HTTP methods', function () {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        foreach ($methods as $method) {
            // Clean previous test
            File::deleteDirectory($this->testFunctionsPath);
            File::ensureDirectoryExists($this->testFunctionsPath);

            $functionPath = $this->testFunctionsPath.'/test-'.$method;
            File::ensureDirectoryExists($functionPath);
            File::put($functionPath.'/function.yml', <<<YAML
function:
  name: TestFunction
  description: A test function
  route: /test
  method: {$method}
  runtime: php:8.4
  entrypoint: index.php
YAML);

            $this->artisan('functions:scan', ['--path' => $this->testFunctionsPath, '--validate' => true])
                ->expectsOutputToContain('✓ Validation complete')
                ->assertSuccessful();
        }
    });
});
