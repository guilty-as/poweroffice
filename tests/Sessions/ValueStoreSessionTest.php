<?php


namespace Guilty\Poweroffice\Tests\Sessions;

use Guilty\Poweroffice\Sessions\SessionInterface;
use Guilty\Poweroffice\Sessions\ValueStoreSession;
use Guilty\Poweroffice\Tests\TestCase;
use Spatie\Valuestore\Valuestore;

class ArraySessionTest extends TestCase
{
    protected function getFreshTestValueStoreSession()
    {
        $path = "../../build/poweroffice.json";

        if (file_exists($path)) {
            unlink($path);
        }

        return new ValueStoreSession(Valuestore::make($path));
    }

    /** @test */
    public function by_default_the_session_is_not_valid()
    {
        $session = $this->getFreshTestValueStoreSession();

        $this->assertFalse($session->isValid());
    }

    /** @test */
    public function setFromResponse_populates_data_correctly()
    {
        $date = new \DateTimeImmutable();
        $response = [
            "expires_in" => 601, //
            "access_token" => "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa",
            "refresh_token" => "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb",
        ];

        $session = $this->getFreshTestValueStoreSession();
        ;
        $session->setFromResponse($response);

        $this->assertEquals($response["access_token"], $session->getAccessToken());
        $this->assertEquals($response["refresh_token"], $session->getRefreshToken());
        $this->assertInstanceOf(\DateTimeImmutable::class, $session->getExpireDate());

        // This might fail if this test runs slow...
        $this->assertEquals(10, $date->diff($session->getExpireDate())->format("%i"));
    }

    /** @test */
    public function given_we_have_an_expire_date_that_is_in_the_future_our_session_has_not_expired()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());

        $this->assertFalse($session->hasExpired());
    }

    /** @test */
    public function given_we_have_an_expire_date_that_is_in_the_past_our_session_has_expired()
    {
        $date = new \DateTime("-10 minutes");

        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());
        $session->setExpireDate($date);

        $this->assertTrue($session->hasExpired());
    }

    /** @test */
    public function if_we_have_a_refresh_token_we_can_refresh_our_access_token()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());

        $this->assertTrue($session->canRefresh());
    }

    /** @test */
    public function disconnecting_the_session_clears_all_data()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());

        $session->disconnect();

        $this->assertNull($session->getAccessToken());
        $this->assertNull($session->getRefreshToken());
        $this->assertNull($session->getExpireDate());
    }

    /** @test */
    public function a_disconnected_session_is_invalid()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());

        $session->disconnect();

        $this->assertFalse($session->isValid());
    }

    /** @test */
    public function a_disconnected_session_cannot_be_refreshed()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setFromResponse($this->fakeAccessTokenResponse());

        $session->disconnect();

        $this->assertFalse($session->canRefresh());
    }

    /** @test */
    public function sanity_check_for_getters_and_setters()
    {
        $session = $this->getFreshTestValueStoreSession();
        $session->setAccessToken("ACCESS");
        $session->setRefreshToken("REFRESH");
        $session->setExpireDate($date = new \DateTime("+10 minutes"));

        $this->assertEquals("ACCESS", $session->getAccessToken());
        $this->assertEquals("REFRESH", $session->getRefreshToken());

        $this->assertInstanceOf(\DateTimeImmutable::class, $session->getExpireDate());
        $this->assertEquals(
            $date->format(SessionInterface::EXPIRES_AT_DATE_FORMAT),
            $session->getExpireDate()->format(SessionInterface::EXPIRES_AT_DATE_FORMAT)
        );
    }
}
