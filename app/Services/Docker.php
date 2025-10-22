<?php

namespace App\Services;

use App\Data\Config\RuntimeConfig;
use App\Models\Run;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class Docker
{
    /**
     * Get all currently running Docker processes.
     *
     * @return array<int, array<string, string>>
     */
    public function processes(): array
    {
        $result = Process::run(
            'docker ps --format "{{json .}}"'
        )
            ->throw();

        if (! $result->successful()) {
            return [];
        }

        $output = trim($result->output());

        if (empty($output)) {
            return [];
        }

        $lines     = explode("\n", $output);
        $processes = [];

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $process = json_decode($line, true);

            if (is_array($process)) {
                $processes[] = $process;
            }
        }

        return $processes;
    }

    /**
     * Check if a Docker process with a name starting with the given string is running.
     */
    public function isProcessRunning(string $name): bool
    {
        $processes = $this->processes();

        foreach ($processes as $process) {
            if (isset($process['Names']) && str_starts_with($process['Names'], $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get detailed information about a Docker image.
     *
     * @return array<string, mixed>|null
     */
    public function getImage(string $name): ?array
    {
        $result = Process::run(
            "docker image inspect {$name}"
        );

        if (! $result->successful()) {
            return null;
        }

        $output = trim($result->output());

        if (empty($output)) {
            return null;
        }

        $data = json_decode($output, true);

        if (! is_array($data) || empty($data)) {
            return null;
        }

        return $data[0] ?? null;
    }

    /**
     * Get detailed information about a Docker container.
     *
     * @return array<string, mixed>|null
     */
    public function getContainer(string $name): ?array
    {
        $result = Process::run(
            "docker container inspect {$name}"
        )
            ->throw();

        if (! $result->successful()) {
            return null;
        }

        $output = trim($result->output());

        if (empty($output)) {
            return null;
        }

        $data = json_decode($output, true);

        if (! is_array($data) || empty($data)) {
            return null;
        }

        return $data[0] ?? null;
    }

    /**
     * Build a Docker image from a runtime configuration.
     */
    public function buildImage(RuntimeConfig $runtimeConfig): Run
    {
        $command = sprintf(
            'docker build -t %s %s %s',
            escapeshellarg($runtimeConfig->getDockerImageTag()),
            $this->getBuildArgs($runtimeConfig),
            escapeshellarg(storage_path("config/runtimes/{$runtimeConfig->path}"))
        );

        $run = Run::create([
            'runtime_path' => $runtimeConfig->path,
            'started_at'   => microtime(true),
            'status'       => 'running',
            'command'      => $command,
        ]);

        Log::info("Building Docker image with command: {$command}");

        $result = Process::env([
            'DOCKER_CONFIG' => '/tmp/.docker', // TODO: Why is this here
        ])
            ->run($command)
            ->throw();

        $run->update([
            'status'     => 'completed',
            'stopped_at' => microtime(true),
            'is_success' => $result->successful(),
        ]);

        return $run;
    }

    /**
     * Generate Docker build arguments.
     */
    private function getBuildArgs(RuntimeConfig $runtime)
    {
        return collect($runtime->build['args'] ?? [])
            ->map(fn ($value, $key) => '--build-arg ' . $key . '=' . $value)
            ->values()
            ->implode(' ');
    }
}
