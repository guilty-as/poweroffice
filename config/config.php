<?php


return [
    'application_key' => env("POWEROFFICE_APPLICATION_KEY"),
    'client_key' => env("POWEROFFICE_CLIENT_KEY"),
    'test_mode' => env("POWEROFFICE_TEST_MODE"),
    'store_path' => storage_path("poweroffice.json"),
];