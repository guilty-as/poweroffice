<?php

namespace Guilty\Poweroffice;

use Guilty\Poweroffice\Interfaces\PowerofficeSessionInterface;
use Guilty\Poweroffice\Services\PowerofficeService;
use Guilty\Poweroffice\Services\PowerofficeSession;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Spatie\Valuestore\Valuestore;

class PowerofficeProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('apsis.php'),
            ], 'config');
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'apsis');


        $this->app->singleton(PowerofficeSessionInterface::class, function () {
            return Valuestore::make(config("poweroffice.store_path"));
        });

        $this->app->singleton(PowerofficeSession::class, function () {
            return new PowerofficeSession(app(PowerofficeSessionInterface::class));
        });

        $this->app->singleton(PowerofficeService::class, function () {
            return new PowerofficeService(
                app(Client::class),
                app(PowerofficeSession::class),
                config('services.poweroffice.application_key'),
                config('services.poweroffice.client_key'),
                config('services.poweroffice.test_mode', true)
            );
        });
    }

    public function provides()
    {
        return [
            PowerofficeSessionInterface::class,
            PowerofficeSession::class,
            PowerofficeService::class,
        ];
    }
}
