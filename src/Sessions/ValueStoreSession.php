<?php

namespace Guilty\Poweroffice\Sessions;

use Spatie\Valuestore\Valuestore;

class ValueStoreSession extends AbstractSession
{
    /**
     * @var \Spatie\Valuestore\Valuestore
     */
    protected $store;

    public function __construct(Valuestore $store)
    {
        $this->store = $store;
    }

    public function setAccessToken($accessToken)
    {
        $this->store->put($this->keyName(self::KEY_ACCESS_TOKEN), $accessToken);
    }

    public function getAccessToken()
    {
        return $this->store->get($this->keyName(self::KEY_ACCESS_TOKEN));
    }

    public function setRefreshToken($refreshToken)
    {
        $this->store->put($this->keyName(self::KEY_REFRESH_TOKEN), $refreshToken);
    }

    public function getRefreshToken()
    {
        return $this->store->get($this->keyName(self::KEY_REFRESH_TOKEN));
    }

    public function disconnect()
    {
        $this->store->flushStartingWith(self::KEY_PREFIX);
    }

    public function setExpireDate(\DateTime $expireDate)
    {
        $this->store->put($this->keyName(self::KEY_EXPIRES_AT), $expireDate->format(self::EXPIRES_AT_DATE_FORMAT));
    }

    public function getExpireDate()
    {
        $date = $this->store->get($this->keyName(self::KEY_EXPIRES_AT));
        return \DateTimeImmutable::createFromFormat(self::EXPIRES_AT_DATE_FORMAT, $date);
    }
}
