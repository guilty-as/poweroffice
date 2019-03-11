<?php


namespace Guilty\Poweroffice\Interfaces;


interface PowerofficeSessionInterface
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