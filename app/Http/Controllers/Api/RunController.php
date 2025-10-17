<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Config\FunctionConfig;
use App\Exceptions\FunctionNotFoundException;
use App\Exceptions\RuntimeConfigurationException;
use App\Http\Controllers\Controller;
use App\Models\Variable;
use App\Services\Config;
use App\Services\Docker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Yaml\Yaml;

class RunController extends Controller
{
    public function __construct(
        protected Config $config,
        protected Docker $docker
    ) {}

    /**
     * Run and return the result
     *
     * @throws RuntimeConfigurationException
     * @throws FunctionNotFoundException
     */
    public function handle(): string
    {
        $function = $this->getFunction();

        $command = sprintf('docker run --rm -v %s:/app %s %s',
            escapeshellarg(config('dysfunctional.host_path') . $function->getBasePath()),
            implode(' ', $this->getEnvironment($function)),
            $function->runtime()->getDockerImageTag()
        );

        Log::info('Running command: ' . $command);

        $result = Process::run($command)->throw();

        Log::info('Command finished', [
            'successful' => $result->successful(),
            'failed' => $result->failed(),
            'exitCode' => $result->exitCode(),
        ]);

        return $result->output() ?? $result->errorOutput();
    }

    private function replaceSecretsAndVariables(string $yamlContent, array $secrets, array $variables): string
    {
        // Pattern to match $(secret.KEY:-default_value} or $(variable.KEY:-default_value}
        $pattern = '/\$\((secret|variable)\.([A-Z_]+):-([^}]+)\}/';

        return preg_replace_callback($pattern, function ($matches) use ($secrets, $variables) {
            $type = $matches[1];        // 'secret' or 'variable'
            $key = $matches[2];          // e.g., 'PLEX_TOKEN'
            $defaultValue = $matches[3]; // e.g., 'your_plex_token_here'

            // Determine which array to look in
            $source = ($type === 'secret') ? $secrets : $variables;

            // Use the value from the array if it exists, otherwise use the default
            $value = $source[$key] ?? $defaultValue;

            // Return the value wrapped in quotes if it's not already quoted
            // and if the original default was quoted
            return '"' . $value . '"';
        }, $yamlContent);
    }

    private function getEnvironment(FunctionConfig $function): array
    {
        $variables = Variable::findByMatchingPath($function->path);

        $environment = Yaml::parse($this->replaceSecretsAndVariables(
            yamlContent: $function->getRawYaml(),
            secrets: $variables['secrets'],
            variables: $variables['variables']
        ))['function']['environment'] ?? [];

        return collect([
            'HTTP_REQUEST_HEADERS' => json_encode(getallheaders()),
            'HTTP_REQUEST_INPUT' => json_encode(request()->all()),
        ])
            ->merge($environment)
            ->map(fn ($value, $key) => '-e ' . $key . '=' . escapeshellarg(
                is_bool($value) ? ($value ? 'true' : 'false') : (string) $value
            ))
            ->values()
            ->toArray();
    }

    /**
     * @throws FunctionNotFoundException
     * @throws RuntimeConfigurationException
     */
    private function getFunction(): FunctionConfig
    {
        $function = $this->config->functionMatchingRoute(
            method: request()->method(),
            uri: request()->uri()
        );

        if ($function === null) {
            Log::error('No function matches this route: ' . request()->method() . ' ' . request()->uri());

            throw new FunctionNotFoundException('No function matches this route');
        }

        if ($this->docker->getImage($function->runtime()->getDockerImageTag()) === null) {
            Log::info('Cannot find image for ' . $function->runtime()->getDockerImageTag());

            $this->docker->buildImage($function->runtime());
        }

        if ($function->getEntrypoint() === null) {
            Log::error('Entrypoint file does not exist: ' . $function->getEntrypoint());

            throw new RuntimeConfigurationException(
                'Entrypoint file does not exist: ' . $function->getEntrypoint()
            );
        }

        return $function;
    }
}
