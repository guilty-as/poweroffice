<?php


namespace Guilty\Poweroffice\Tests;


use Guilty\Poweroffice\Services\PowerofficeService;
use Guilty\Poweroffice\Sessions\ArraySession;
use GuzzleHttp\Client;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function fakeAccessTokenResponse()
    {
        return [
            "expires_in" => 600, // 10 Minutes defined in seconds
            "access_token" => "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa",
            "refresh_token" => "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb",
        ];
    }

    public function getTestService()
    {
        $client = new Client();
        $session = new ArraySession();
        $clientKey = "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa";
        $applicationKey = "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb";

        return new PowerofficeService($client, $session, $clientKey, $applicationKey, true);
    }
}