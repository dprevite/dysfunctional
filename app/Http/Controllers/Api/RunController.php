<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\RuntimeConfigurationException;
use App\Http\Controllers\Controller;
use App\Services\Config;
use App\Services\Docker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class RunController extends Controller
{
    public function __construct(
        protected Config $config,
        protected Docker $docker
    )
    {
    }

    /**
     * Run and return the result
     * @throws RuntimeConfigurationException
     */
    public function handle()
    {
        // TODO: Match the function based on the route and method
        $function = $this->config->function('testing/something');

        if ($this->docker->getImage($function->runtime()->getDockerImageTag()) === null) {
            Log::info('Cannot find image for ' . $function->runtime()->getDockerImageTag());

            $this->docker->buildImage($function->runtime());
        }

//        dd(
//            $function->getEntrypoint(),
//            $function,
//            $function->runtime(),
//            $function->runtime()->getDockerFile(),
//            $function->runtime()->getDockerImageTag(),
//            $this->docker->getImage($function->runtime()->getDockerImageTag())
//        );

        $environment = [];

        $environment[] = '-e HTTP_REQUEST_HEADERS=' . escapeshellarg(json_encode(getallheaders()));
        $environment[] = '-e HTTP_REQUEST_INPUT=' . escapeshellarg(json_encode(request()->all()));

        if ($function->getEntrypoint() === null) {
            Log::error('Entrypoint file does not exist: ' . $function->getEntrypoint());

            throw new RuntimeConfigurationException(
                'Entrypoint file does not exist: ' . $function->getEntrypoint()
            );
        }

        $command = sprintf('docker run --rm -v %s:/app/entrypoint.php %s %s',
            escapeshellarg(config('dysfunctional.host_path') . '/' . $function->getEntrypoint()),
            implode(' ', $environment),
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
}
