<?php

namespace App\Services;

use App\Data\Config\RuntimeConfig;
use Illuminate\Contracts\Process\ProcessResult;
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
        $result = Process::run('docker ps --format "{{json .}}"');

        if (!$result->successful()) {
            return [];
        }

        $output = trim($result->output());

        if (empty($output)) {
            return [];
        }

        $lines = explode("\n", $output);
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
        $result = Process::run("docker image inspect {$name}");

        if (!$result->successful()) {
            return null;
        }

        $output = trim($result->output());

        if (empty($output)) {
            return null;
        }

        $data = json_decode($output, true);

        if (!is_array($data) || empty($data)) {
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
        $result = Process::run("docker container inspect {$name}");

        if (!$result->successful()) {
            return null;
        }

        $output = trim($result->output());

        if (empty($output)) {
            return null;
        }

        $data = json_decode($output, true);

        if (!is_array($data) || empty($data)) {
            return null;
        }

        return $data[0] ?? null;
    }

    /**
     * Build a Docker image from a runtime configuration.
     */
    public function buildImage(RuntimeConfig $runtimeConfig): ProcessResult
    {
        $tag = $runtimeConfig->getDockerImageTag();
        $buildPath = storage_path("config/runtimes/{$runtimeConfig->path}");

        return Process::env([
            'DOCKER_CONFIG' => '/tmp/.docker',
        ])
            ->run("docker build -t {$tag} {$buildPath}")
            ->throw();
    }
}
