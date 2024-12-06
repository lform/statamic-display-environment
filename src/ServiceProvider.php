<?php

namespace Lform\EnvDisplay;

use Lform\EnvDisplay\Middleware\InjectDisplay;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        if (! file_exists(config_path('env-display.php'))) {
            $this->publishes([
                __DIR__ . '/../config/env-display.php' => config_path('env-display.php'),
            ], 'env-display');
        }

        $this
            ->bootAddonViews()
            ->bootAddonMiddleware();
    }

    protected function bootAddonViews(): self
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lform-env-display');

        return $this;
    }

    protected function bootAddonMiddleware(): self
    {
        $this->app['router']->pushMiddlewareToGroup('statamic.cp', InjectDisplay::class);

        return $this;
    }

    protected $stylesheets = [
        __DIR__.'/../resources/css/cp.css'
    ];

}
