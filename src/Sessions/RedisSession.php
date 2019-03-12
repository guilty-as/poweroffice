<?php


namespace Guilty\Poweroffice\Sessions;

use Predis\Client;


class RedisSession extends AbstractSession
{
    /**
     * @var \Predis\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $keyNamePrefix;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setAccessToken($accessToken)
    {
        $this->client->set($this->keyName(self::KEY_ACCESS_TOKEN), $accessToken);
    }

    public function getAccessToken()
    {
        return $this->client->get($this->keyName(self::KEY_ACCESS_TOKEN));
    }

    public function setRefreshToken($refreshToken)
    {
        $this->client->set($this->keyName(self::KEY_REFRESH_TOKEN), $refreshToken);
    }

    public function getRefreshToken()
    {
        return $this->client->get($this->keyName(self::KEY_REFRESH_TOKEN));
    }

    public function disconnect()
    {
        $this->client->del([
            $this->keyName(self::KEY_ACCESS_TOKEN),
            $this->keyName(self::KEY_REFRESH_TOKEN),
            $this->keyName(self::KEY_EXPIRES_AT),
        ]);
    }

    public function setExpireDate(\DateTime $expireDate)
    {
        $this->client->set(
            $this->keyName(self::KEY_EXPIRES_AT),
            $expireDate->format(self::EXPIRES_AT_DATE_FORMAT)
        );
    }

    /** @return \DateTimeImmutable */
    public function getExpireDate()
    {
        try {
            $date = $this->client->get($this->keyName(self::KEY_EXPIRES_AT));
            return \DateTimeImmutable::createFromFormat(self::EXPIRES_AT_DATE_FORMAT, $date);
        } catch (\Exception $exception) {
            return null;
        }
    }
}