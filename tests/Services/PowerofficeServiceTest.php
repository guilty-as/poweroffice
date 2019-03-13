<?php


namespace Guilty\Poweroffice\Tests\Services;

use Guilty\Poweroffice\Exceptions\InvalidClientException;
use Guilty\Poweroffice\Services\PowerofficeService;
use Guilty\Poweroffice\Sessions\ArraySession;
use Guilty\Poweroffice\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PowerofficeServiceTest extends TestCase
{

    /** @test */
    public function the_service_will_throw_an_InvalidClientException_if_provided_an_invalid_key_trying_to_get_an_access_token()
    {
        $service = $this->getTestService();

        $this->expectException(InvalidClientException::class);

        $service->getAccessToken();
    }

    /** @test */
    public function the_service_will_populate_the_session_with_the_correct_data()
    {
        $mock = \Mockery::mock(Client::class);
        $mock->shouldReceive("post")->andReturn(
            new Response(200, [], json_encode($this->fakeAccessTokenResponse()))
        );

        $session = new ArraySession();
        $clientKey = "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa";
        $applicationKey = "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb";

        $service = new PowerofficeService($mock, $session, $clientKey, $applicationKey, true);

        $service->getAccessToken();

        $this->assertEquals("test-access-token", $session->getAccessToken());
        $this->assertEquals("test-refresh-token", $session->getRefreshToken());
    }
}
