<?php

namespace Guilty\Poweroffice;

use Guilty\Poweroffice\Services\PowerofficeService;
use Illuminate\Support\Facades\Facade;

class PowerofficeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PowerofficeService::class;
    }
}
