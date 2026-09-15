<?php

namespace Ugarit\Ranger;

use Heritage\Support\ServiceProvider;
use Ugarit\Ranger\Collectors\BroadcastChannels;
use Ugarit\Ranger\Collectors\BroadcastEvents;
use Ugarit\Ranger\Collectors\Enums;
use Ugarit\Ranger\Collectors\Models;
use Ugarit\Ranger\Collectors\Routes;
use Ugarit\Ranger\Support\Ignores;
use Ugarit\Surveyor\Support\Markers;

class RangerServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->app->singleton(Enums::class);
        $this->app->singleton(Models::class);
        $this->app->singleton(Routes::class);
        $this->app->singleton(BroadcastChannels::class);
        $this->app->singleton(BroadcastEvents::class);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerPublishing();

        Markers::registerConditionResolver([Ignores::class, 'evaluate']);
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ranger.php' => config_path('ranger.php'),
            ], 'ranger-config');
        }
    }
}
