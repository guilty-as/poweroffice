# PowerOffice PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guilty/poweroffice.svg?style=flat-square)](https://packagist.org/packages/guilty/poweroffice)
[![Total Downloads](https://img.shields.io/packagist/dt/guilty/poweroffice.svg?style=flat-square)](https://packagist.org/packages/guilty/poweroffice)


Poweroffice API client, used it to talk to the [PowerOffice](https://poweroffice.no/) API: https://api.poweroffice.net/Web/docs/index.html


# Installation

You can install the package via composer:

```bash
composer require guilty/poweroffice
```

# Usage 

## Standalone

You can use the package as a standalone PHP Package, here is a simple example:

```php
<?php


use Guilty\Poweroffice\Services\PowerofficeService;
use Guilty\Poweroffice\Sessions\ValueStoreSession;
use GuzzleHttp\Client;
use Spatie\Valuestore\Valuestore;


$client = new Client();
$store = Valuestore::make(config("poweroffice.store_path"));
$session = new ValueStoreSession($store);
$service = new PowerOfficeService(
    $client,
    $session,
    "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa", // Application Key
    "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb", // Client Key 
    true // Test Mode
);
```


## Laravel

You can publish the config file like so
```
php artisan vendor:publish --provider="Guilty\Poweroffice\PowerofficeServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [
    'application_key' => env("POWEROFFICE_APPLICATION_KEY"),
    'client_key' => env("POWEROFFICE_CLIENT_KEY"),
    'test_mode' => env("POWEROFFICE_TEST_MODE"),
    'store_path' => storage_path("poweroffice.json"), // Used with the ValueStoreSession
];
```

To get started, add the following environment variable to your .env file:

```dotenv
POWEROFFICE_APPLICATION_KEY=aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa
POWEROFFICE_CLIENT_KEY=bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb
POWEROFFICE_TEST_MODE=true
```

## Session Implementations

The PowerOffice API uses the ["client_credentials" grant type](https://www.oauth.com/oauth2-servers/access-tokens/client-credentials/) for authentication, to keep 
track of the "session" (access token, refresh token and expire date) we use a 
Session class that stores these values for us, out of the box the following 
session implementations are provided:


### Provided Session Implementations

- ArraySession - Used for testing
- ValueStoreSession - Can be used in production, saves all data in a json file defined in the "poweroffice.store_path" config option, uses [Spatie's ValueStore package](https://github.com/spatie/valuestore) 
- RedisSession - Can be used in production, saves all data in a redis cache, keys are prefixed with ```POWEROFFICE_SESSION_{KEYNAME}```, expects a [Predis Client](https://packagist.org/packages/predis/predis). 


### Implementing your own session class

Create a new class that ```implements``` the ```SessionInterface``` interface and add the required methods, the actual implementation is up to you, you can put the data in a database, a file, in redis, or whatever you need.

**OR...**
 
extend the ```AbstractSession``` which implements some of the methods for you.

Here is the interface you need to implement.

```php
<?php

namespace Guilty\Poweroffice\Interfaces;

interface SessionInterface
{
    public function setAccessToken($accessToken);
    public function getAccessToken();
    public function setRefreshToken($refreshToken);
    public function getRefreshToken();
    public function canRefresh();
    public function disconnect();
    public function setExpireDate(\DateTime $expireDate);
    /** @return \DateTimeImmutable */
    public function getExpireDate();
    public function hasExpired();
    public function isValid();
    public function setFromResponse($response);
}
```

Here is an example extending the AbstractSession.
```php
<?php

use Guilty\Poweroffice\Sessions\AbstractSession;

// Or whatever else you want to store your session
class ExcelSpreadsheetSession extends AbstractSession
{
    public function setAccessToken($accessToken) { /* TODO: Implement */ }
    public function getAccessToken() { /* TODO: Implement */ }
    public function setRefreshToken($refreshToken) { /* TODO: Implement */ }
    public function getRefreshToken() { /* TODO: Implement */ }
    public function disconnect() { /* TODO: Implement */ }
    public function setExpireDate(\DateTime $expireDate) { /* TODO: Implement */ }
    public function getExpireDate() { /* TODO: Implement */ } 
}
```

## Using the service class

You can use the ```performRequest()``` method directly if you just want to use this package as a thin wrapper for Guzzle that handles the oAuth session storage and maintenance, here is an example:

```php
<?php
 
require "./vendor/autoload.php";


use Guilty\Poweroffice\Services\PowerofficeService;
use GuzzleHttp\Client;
use Guilty\Poweroffice\Sessions\ArraySession;

$service = new PowerOfficeService(
    new Client(),
    new ArraySession(), 
    "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa", // Application Key
    "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb", // Client Key 
    true // Test Mode
);

$customer = $service->performRequest("post", "/Customer", [
    "json" => [
        "firstName" => "John",
        "lastName" => "Smith",
        "emailAddress" => "johnsmith@example.com",
        "invoiceEmailAddress" => "johnsmith@example.com",
        "isArchived" => false,
        "isPerson" => true,
        "invoiceDeliveryType" => 1, // PDF By Email
        "since" => (new DateTimeImmutable())->format("Y-m-d"),
        "mailAddress" => $address = [
            "address1" => "123 Fakestreet",
            "city" => "Lazytown",
            "zipCode" => "1234",
            "countryCode" => "NO", // ISO Country Code (norway)
            "isPrimary" => true,
        ],
        "streetAddresses" => [$address], // Must be an array
    ]
]);

// All responses will include a success key, that can be used for error handling
if ($response["success"] === false) {
    throw new Exception("Customer could not be created");
}
```  

Or you can use the methods provided, that will prefill the method and path for you, as well as make it easier to provide certain data (like start and end dates).

```php
<?php
 
require "./vendor/autoload.php";


use Guilty\Poweroffice\Services\PowerofficeService;
use GuzzleHttp\Client;
use Guilty\Poweroffice\Sessions\ArraySession;

$service = new PowerOfficeService(
    new Client(),
    new ArraySession(),
    "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa", // Application Key
    "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb", // Client Key 
    true // Test Mode
);

$customer = $service->createCustomer([
    "json" => [
        "firstName" => "John",
        "lastName" => "Smith",
        "emailAddress" => "johnsmith@example.com",
        "invoiceEmailAddress" => "johnsmith@example.com",
        "isArchived" => false,
        "isPerson" => true,
        "invoiceDeliveryType" => 1, // PDF By Email
        "since" => (new DateTimeImmutable())->format("Y-m-d"),
        "mailAddress" => $address = [
            "address1" => "123 Fakestreet",
            "city" => "Lazytown",
            "zipCode" => "1234",
            "countryCode" => "NO", // ISO Country Code (norway)
            "isPrimary" => true,
        ],
        "streetAddresses" => [$address], // Must be an array
    ]
]);

// All responses will include a success key, that can be used for error handling
if ($response["success"] === false) {
    throw new Exception("Customer could not be created");
}
```  

# Note about oData filtering

It seems that PowerOffice's API is case sensitive when it comes to the field names in oData filtering. 


# TODO

The following services are implemented in the API client wrapper: 

## Sessions

- [x] ArraySession (testing)
- [x] ValueStoreSession (production)
- [x] RedisSession (production)

## Functionality

- [ ] oData filter builder

## Services 

- [x] Bank/BankTransfer
- [x] Bank/ClientBankAccount
- [x] Blob
- [x] BrandingTheme
- [x] Client
- [ ] ClientAuth
- [x] ContactGroup
- [ ] Customer
- [ ] DebtCollection
- [ ] Department
- [ ] Employee
- [x] ExternallyDeliverableInvoice
- [x] GeneralLedgerAccount
- [ ] Import
- [x] InvoiceAttachment
- [x] JournalEntryVoucher
- [x] Location
- [x] OutgoingInvoice
- [x] PartyBankAccount
- [x] PartyContactPerson
- [x] Payroll/PayItem
- [x] Payroll/SalaryLine
- [x] Product
- [x] ProductGroup
- [ ] Project
- [x] ProjectActivity
- [x] ProjectTeamMember
- [x] RecurringInvoice
- [x] Reporting/AccountTransactions
- [ ] Reporting/CustomerLedger
- [ ] Reporting/InvoiceJournal
- [ ] Reporting/SupplierLedger
- [x] Reporting/TrialBalance
- [x] Reporting/Usage
- [x] SubledgerNumberSeries
- [ ] Supplier
- [x] TimeTracking/Activity
- [x] TimeTracking/HourType
- [x] TimeTracking/TimeTrackingEntry
- [x] VatCode


# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

Brought to you by [Guilty AS](https://guilty.no)

The Poweroffice logo and Trademark is the property of [Poweroffice AS](https://poweroffice.no/)
