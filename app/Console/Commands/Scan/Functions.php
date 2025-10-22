<?php

declare(strict_types=1);

namespace App\Console\Commands\Scan;

use App\Actions\Scan\FunctionScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Scan and list all function definitions in the functions directory.
 */
final class Functions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:functions
                            {--path= : Custom path to functions directory}
                            {--validate : Validate function definitions against schema}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and list all function definitions in the functions directory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scanner = new FunctionScanner(
            validate: $this->option('validate')
        );

        $path = $this->option('path');

        if ($path && ! File::isDirectory($path)) {
            $this->error("Directory not found: {$path}");

            return self::FAILURE;
        }

        $results = $scanner->scan($path);

        if (empty($results)) {
            $this->warn(sprintf('No function configs found in %s.', $path ?? storage_path('config/functions')));

            return self::SUCCESS;
        }

        $this->info('Found ' . count($results) . " function(s):\n");

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
        } else {
            $this->displayResults($results);
        }

        return self::SUCCESS;
    }

    /**
     * Display functions in a formatted table.
     *
     * @param  array<int, array<string, mixed>>  $results
     */
    protected function displayResults(array $results): void
    {
        $headers = ['Path', 'Name', 'Route', 'Method', 'Runtime'];
        $rows    = array_map(fn ($f) => [
            $f['path'],
            $f['name'],
            $f['route'],
            $f['method'],
            $f['runtime'],
        ], $results);

        $this->table($headers, $rows);

        if ($this->option('validate')) {
            $hasErrors = false;
            foreach ($results as $result) {
                if (! empty($result['validation_errors'])) {
                    $hasErrors = true;
                    $this->newLine();
                    $this->warn("Validation errors in {$result['file']}:");
                    foreach ($result['validation_errors'] as $error) {
                        $this->line("  • {$error}");
                    }
                }
            }

            if (! $hasErrors) {
                $this->newLine();
                $this->info('✓ Validation complete');
            }
        }
    }
}
