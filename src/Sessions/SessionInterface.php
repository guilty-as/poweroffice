<?php


namespace Guilty\Poweroffice\Sessions;


interface SessionInterface
{
    const KEY_PREFIX = "POWEROFFICE_SESSION_";
    const KEY_ACCESS_TOKEN = "ACCESS_TOKEN";
    const KEY_REFRESH_TOKEN = "REFRESH_TOKEN";
    const KEY_EXPIRES_AT = "EXPIRES_AT";
    const EXPIRES_AT_DATE_FORMAT = "Y-m-d H:i:s";

    public function setAccessToken($accessToken);

    public function getAccessToken();

    public function setRefreshToken($refreshToken);

    public function getRefreshToken();

    public function canRefresh();

    public function disconnect();

    public function setExpireDate(\DateTime $expireDate);

    public function getExpireDate();

    public function hasExpired();

    public function isValid();

    public function setFromResponse($response);
}