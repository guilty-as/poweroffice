<?php

namespace Guilty\Poweroffice\Sessions;


use Carbon\Carbon;
use Guilty\Poweroffice\Interfaces\SessionInterface;
use Spatie\Valuestore\Valuestore;

/**
 * Implements the sessioninterface using the ValueStore package from Spatie,
 * it is essentially just a JSON file with a nice interface around it.
 *
 * Class ValueStoreSession
 * @package Guilty\Poweroffice\Sessions
 */
class ValueStoreSession implements SessionInterface
{
    private $storeKeyPrefix = "POWEROFFICE_SESSION_";
    private $accessTokenStoreKey = "ACCESS_TOKEN";
    private $refreshTokenStoreKey = "REFRESH_TOKEN";
    private $expiresAtStoreKey = "EXPIRES_AT";
    private $dateSerializationFormat = "Y-m-d H:i:s";

    /**
     * @var \Spatie\Valuestore\Valuestore
     */
    protected $store;

    public function __construct(Valuestore $store)
    {
        $this->store = $store;
    }

    private function keyName($key)
    {
        return $this->storeKeyPrefix . $key;
    }

    public function setAccessToken($accessToken)
    {
        $this->store->put($this->keyName($this->accessTokenStoreKey), $accessToken);
    }

    public function getAccessToken()
    {
        return $this->store->get($this->keyName($this->accessTokenStoreKey));
    }

    public function setRefreshToken($refreshToken)
    {
        $this->store->put($this->keyName($this->refreshTokenStoreKey), $refreshToken);
    }

    public function getRefreshToken()
    {
        return $this->store->get($this->keyName($this->refreshTokenStoreKey));
    }

    public function disconnect()
    {
        $this->store->flushStartingWith($this->storeKeyPrefix);
    }

    public function setExpireDate(\DateTime $expireDate)
    {
        $this->store->put($this->keyName($this->expiresAtStoreKey), $expireDate->format($this->dateSerializationFormat));
    }

    public function getExpireDate()
    {
        $date = $this->store->get($this->keyName($this->expiresAtStoreKey));
        return \DateTimeImmutable::createFromFormat($this->dateSerializationFormat, $date);
    }

    public function hasExpired()
    {
        $expireDate = $this->getExpireDate();
        $now = new \DateTimeImmutable();

        return $expireDate < $now;
    }

    public function isValid()
    {
        return $this->getAccessToken() && $this->hasExpired() === false;
    }

    public function setFromResponse($response)
    {
        $seconds = $response["expires_in"];
        $date = (new \DateTime())->add(new \DateInterval("P{$seconds}S"));

        $this->setExpireDate($date);
        $this->setAccessToken($response["access_token"]);
        $this->setRefreshToken($response["refresh_token"]);
    }

    public function canRefresh()
    {
        return !!$this->getRefreshToken();
    }
}