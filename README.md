# PowerOffice PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guilty/apsis.svg?style=flat-square)](https://packagist.org/packages/guilty/apsis)
[![Total Downloads](https://img.shields.io/packagist/dt/guilty/apsis.svg?style=flat-square)](https://packagist.org/packages/guilty/apsis)


APSIS API client, used for interacting with the [APSIS](https://www.apsis.com/) API: http://se.apidoc.anpdm.com


## Installation

You can install the package via composer:

```bash
composer require guilty/poweroffice
```


## Laravel

This package is compatible with Laravel

You can publish the config file like so
```
php artisan vendor:publish --provider="Guilty\Apsis\ApsisServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    /*
     * This is the API key for your APSIS account.
     * Read how to generate an api key here:
     * http://se.apidoc.anpdm.com/Help/GettingStarted/Getting%20started
     */
    "api_key" => env("APSIS_API_KEY")
];
```

To get started, add the following environment variable to your .env file:

```
APSIS_API_KEY="your-api-key"
```


You can use the facade like so:
```php

```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

--- 

Brought to you by [Guilty AS](https://guilty.no)

The APSIS logo and Trademark is the property of [APSIS International AB](https://www.apsis.com/)
