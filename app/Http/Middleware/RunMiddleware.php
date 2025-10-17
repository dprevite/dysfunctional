<?php

namespace App\Http\Middleware;

use App\Data\Config\FunctionConfig;
use App\Exceptions\FunctionNotFoundException;
use App\Models\Run;
use App\Services\Config;
use App\Services\Docker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RunMiddleware
{
    public function __construct(
        protected Config $config,
        protected Docker $docker
    )
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     *
     * @throws FunctionNotFoundException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $run = Run::create([
            'requested_at' => microtime(true),
            'uri' => $request->getRequestUri(),
        ]);

        Context::add('run_id', $run->id);
        Context::add('uri', $request->getRequestUri());

        $request->route()->setParameter('run', $run);

        $request->headers->set(
            'X-Dysfunctional-Run-Id',
            $run->id,
        );

        $function = $this->getFunction();

        Context::add('function', $function->path);
        Context::add('runtime', $function->runtime()->path);

        $request->route()->setParameter('function', $function);

        $run->update([
            'function_path' => $function->path,
            'runtime_path' => $function->runtime()->path,
        ]);

        $response = $next($request);

        $run->fresh()->update([
            'responded_at' => microtime(true),
            'is_success' => true,
            'status' => 'completed',
            'response_code' => $response->getStatusCode(),
        ]);

        return $response->header(
            'X-Dysfunctional-Run-Id',
            $run->id,
        );
    }

    /**
     * @throws FunctionNotFoundException
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

        return $function;
    }
}
