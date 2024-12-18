<?php

namespace Lform\EnvDisplay\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InjectDisplay
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() !== 200) {
            return $response;
        }

        try {
            $this->injectEnvironmentDetector($response);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
        }

        return $response;
    }

    protected function injectEnvironmentDetector($response): void
    {
        $env = env("APP_ENV");

        if (!$env) {
            return;
        }

        $detectorConfig = config('env-detector');
        $envConfig = $detectorConfig["detect"][$env];

        if (!$envConfig) {
            return;
        }

        $content = $response->getContent();

        $widget = view('lform-env-detector::index', [
            'env' => $env,
            'config' => $envConfig,
        ]);

        $position = 0;
        while (($navPosition = mb_stripos($content, '<div class="nav-main-inner">', $position)) !== false) {
            $navPosition += mb_strlen('<div class="nav-main-inner">');
            // Inject the widget after each instance
            $content = mb_substr($content, 0, $navPosition) . $widget . mb_substr($content, $navPosition);

            // Move the position forward
            $position = $navPosition;
        }

        $response->setContent($content);
        $response->headers->set('Content-Length', strlen($response->getContent()));
    }
}
