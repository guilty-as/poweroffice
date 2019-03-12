<?php

namespace Guilty\Poweroffice\Sessions;


use Guilty\Poweroffice\Interfaces\SessionInterface;

/**
 * Implements the session interface using an in-memory array, use this for tests and such.
 *
 * Class ArraySession
 * @package Guilty\Poweroffice\Sessions
 */
class ArraySession implements SessionInterface
{
    /**
     * @var array
     */
    protected $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function setAccessToken($accessToken)
    {
        $this->data["access_token"] = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->data["access_token"] ?? null;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->data["refresh_token"] = $refreshToken;
    }

    public function getRefreshToken()
    {
        return $this->data["refresh_token"] ?? null;
    }

    public function canRefresh()
    {
        return !!$this->getRefreshToken();
    }

    public function disconnect()
    {
        $this->data = [];
    }

    public function setExpireDate(\DateTime $expireDate)
    {
        $this->data["refresh_token"] = $expireDate;
    }

    /** @return \DateTimeImmutable */
    public function getExpireDate()
    {
        try {
            return \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $this->data["expire_date"]);
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function hasExpired()
    {
        $expireDate = $this->getExpireDate();
        $now = new \DateTimeImmutable("now");

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

        $this->setExpireDate($date->format("Y-m-d H:i:s"));
        $this->setAccessToken($response["access_token"]);
        $this->setRefreshToken($response["refresh_token"]);
    }
}