<?php


namespace Guilty\Poweroffice\Sessions;


use Guilty\Poweroffice\Interfaces\SessionInterface;

abstract class AbstractSession implements SessionInterface
{
    public function hasExpired()
    {
        $expireDate = $this->getExpireDate();
        $now = new \DateTimeImmutable();

        return $expireDate < $now;
    }

    protected function keyName($key)
    {
        return self::KEY_PREFIX . $key;
    }

    public function isValid()
    {
        return $this->getAccessToken() && $this->hasExpired() === false;
    }

    public function canRefresh()
    {
        return !!$this->getRefreshToken();
    }

    public function setFromResponse($response)
    {
        $seconds = $response["expires_in"];
        $date = (new \DateTime())->add(new \DateInterval("P{$seconds}S"));

        $this->setExpireDate($date->format("Y-m-d H:i:s"));
        $this->setAccessToken($response["access_token"]);
        $this->setRefreshToken($response["refresh_token"]);
    }

    abstract public function setAccessToken($accessToken);

    abstract public function getAccessToken();

    abstract public function setRefreshToken($refreshToken);

    abstract public function getRefreshToken();

    abstract public function disconnect();

    abstract public function setExpireDate(\DateTime $expireDate);

    abstract public function getExpireDate();

}