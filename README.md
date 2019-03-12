# PowerOffice PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guilty/poweroffice.svg?style=flat-square)](https://packagist.org/packages/guilty/poweroffice)
[![Total Downloads](https://img.shields.io/packagist/dt/guilty/poweroffice.svg?style=flat-square)](https://packagist.org/packages/guilty/poweroffice)


Poweroffice API client, used for interacting with the [PowerOffice](https://poweroffice.no/) API: https://api.poweroffice.net/Web/docs/index.html


## Installation

You can install the package via composer:

```bash
composer require guilty/poweroffice
```


## Laravel

This package is compatible with Laravel

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
    'redirect_url' => env("POWEROFFICE_REDIRECT_URL"),
    'test_mode' => env("POWEROFFICE_TEST_MODE"),
    'store_path' => storage_path("poweroffice.json")
];
```

To get started, add the following environment variable to your .env file:

```
POWEROFFICE_APPLICATION_KEY=aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa
POWEROFFICE_CLIENT_KEY=bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb
POWEROFFICE_TEST_MODE=true
```

## Usage 

```php
// TODO: Make usage docs
```

# TODO

The following services are implemented in the API client wrapper: 

[] Bank/BankTransfer
[] Bank/ClientBankAccount
[] Blob
[] BrandingTheme
[] Client
[] ClientAuth
[] ContactGroup
[] Customer
[] DebtCollection
[] Department
[] Employee
[] ExternallyDeliverableInvoice
[] GeneralLedgerAccount
[] Import
[] InvoiceAttachment
[] JournalEntryVoucher
[] Location
[] OutgoingInvoice
[] PartyBankAccount
[] PartyContactPerson
[] Payroll/PayItem
[] Payroll/SalaryLine
[] Product
[] ProductGroup
[] Project
[] ProjectActivity
[] ProjectTeamMember
[] RecurringInvoice
[] Reporting/AccountTransactions
[] Reporting/CustomerLedger
[] Reporting/InvoiceJournal
[] Reporting/SupplierLedger
[] Reporting/TrialBalance
[] Reporting/Usage
[] SubledgerNumberSeries
[] Supplier
[] TimeTracking/Activity
[] TimeTracking/HourType
[] TimeTracking/TimeTrackingEntry
[] VatCode

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

Brought to you by [Guilty AS](https://guilty.no)

The Poweroffice logo and Trademark is the property of [Poweroffice AS](https://poweroffice.no/)
