<?php

namespace Lform\EnvDetector;

use Lform\EnvDetector\Middleware\InjectDetector;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        if (! file_exists(config_path('env-detector.php'))) {
            $this->publishes([
                __DIR__ . '/../config/env-detector.php' => config_path('env-detector.php'),
            ], 'env-detector');
        }

        $this
            ->bootAddonViews()
            ->bootAddonMiddleware();
    }

    protected function bootAddonViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lform-env-detector');

        return $this;
    }

    protected function bootAddonMiddleware(): self
    {
        $this->app['router']->pushMiddlewareToGroup('statamic.cp', InjectDetector::class);

        return $this;
    }

    protected $stylesheets = [
        __DIR__.'/../resources/css/cp.css'
    ];

}
