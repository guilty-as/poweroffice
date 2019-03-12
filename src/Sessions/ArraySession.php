<?php

namespace Guilty\Poweroffice\Sessions;


class ArraySession extends AbstractSession
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
        $this->data[$this->keyName(self::KEY_ACCESS_TOKEN)] = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->data[$this->keyName(self::KEY_ACCESS_TOKEN)] ?? null;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->data[$this->keyName(self::KEY_REFRESH_TOKEN)] = $refreshToken;
    }

    public function getRefreshToken()
    {
        return $this->data[$this->keyName(self::KEY_REFRESH_TOKEN)] ?? null;
    }

    public function disconnect()
    {
        $this->data = [];
    }

    public function setExpireDate(\DateTime $expireDate)
    {
        $this->data[$this->keyName(self::KEY_EXPIRES_AT)] = $expireDate->format(self::EXPIRES_AT_DATE_FORMAT);
    }

    public function getExpireDate()
    {
        try {
            return \DateTimeImmutable::createFromFormat(self::EXPIRES_AT_DATE_FORMAT, $this->data[$this->keyName(self::KEY_EXPIRES_AT)]);
        } catch (\Exception $exception) {
            return null;
        }
    }

}