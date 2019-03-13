<?php

namespace Guilty\Poweroffice;

use Guilty\Poweroffice\Interfaces\SessionInterface;
use Guilty\Poweroffice\Services\PowerofficeService;
use Guilty\Poweroffice\Sessions\ValueStoreSession;
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
                __DIR__ . '/../config/config.php' => config_path('poweroffice.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'poweroffice');


        $this->app->bind(SessionInterface::class, function () {
            $store = Valuestore::make(config("poweroffice.store_path"));
            return new ValueStoreSession($store);
        });


        $this->app->bind(PowerOfficeService::class, function () {
            return new PowerOfficeService(
                app(Client::class),
                app(SessionInterface::class),
                config('services.poweroffice.application_key'),
                config('services.poweroffice.client_key'),
                config('services.poweroffice.test_mode', true)
            );
        });
    }

    public function provides()
    {
        return [
            SessionInterface::class,
            PowerofficeService::class,
        ];
    }
}
