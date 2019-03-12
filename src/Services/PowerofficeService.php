<?php

namespace Guilty\Poweroffice\Services;


use Guilty\Poweroffice\Exceptions\InvalidClientException;
use Guilty\Poweroffice\Exceptions\TooManyRequestsException;
use Guilty\Poweroffice\Exceptions\UnauthorizedException;
use Guilty\Poweroffice\Interfaces\SessionInterface;
use GuzzleHttp\Client;


class PowerofficeService
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Guilty\Poweroffice\Interfaces\SessionInterface
     */
    protected $session;

    protected $apiBaseUrl;
    protected $authBaseUrl;

    protected $applicationKey;
    protected $clientKey;

    protected $accessTokenPath = "/OAuth/Token";
    protected $testMode;

    /**
     * @param \GuzzleHttp\Client $client
     * @param \Guilty\Poweroffice\Interfaces\SessionInterface $session
     * @param string $applicationKey The application key
     * @param string $clientKey The client key
     * @param bool $testMode Should the service hit the test api or the live api, defaults to test mode (true)
     */
    public function __construct(Client $client, SessionInterface $session, $applicationKey, $clientKey, $testMode = true)
    {
        $this->client = $client;
        $this->session = $session;
        $this->applicationKey = $applicationKey;
        $this->clientKey = $clientKey;

        $testMode
            ? $this->useTestMode()
            : $this->useLiveMode();
    }

    public function isTestMode()
    {
        return $this->testMode;
    }

    protected function useLiveMode()
    {
        $this->testMode = false;
        $this->apiBaseUrl = "https://api.poweroffice.net";
        $this->authBaseUrl = "https://go.poweroffice.net";
    }

    protected function useTestMode()
    {
        $this->testMode = true;
        $this->apiBaseUrl = "https://api-demo.poweroffice.net";
        $this->authBaseUrl = "https://godemo.poweroffice.net";
    }

    protected function getAccessTokenUrl()
    {
        return $this->authBaseUrl . $this->accessTokenPath;
    }

    protected function getApiUrl($path)
    {
        return $this->apiBaseUrl . "/" . trim($path, "/");
    }

    protected function getAuthenticationCredentials()
    {
        return [$this->applicationKey, $this->clientKey];
    }

    protected function getAuthorizationHeader()
    {
        return [
            'Authorization' => 'Bearer ' . $this->session->getAccessToken(),
        ];
    }

    public function refreshIfExpired()
    {
        if ($this->session->hasExpired() && $this->session->canRefresh()) {
            $this->refreshAccessCode();
        }
    }

    /**
     * @return void
     * @throws \Guilty\Poweroffice\Exceptions\InvalidClientException
     */
    public function refreshAccessCode()
    {
        $request = $this->client->post($this->getAccessTokenUrl(), [
            'http_errors' => false,
            "auth" => $this->getAuthenticationCredentials(),
            "form_params" => [
                "grant_type" => "refresh_token",
                "refresh_token" => $this->session->getRefreshToken(),
            ],
        ]);

        $response = json_decode($request->getBody(), true);

        if ($request->getStatusCode() === 400 && $response["error"] == "invalid_client") {
            throw new InvalidClientException("The client is invalid");
        }

        $this->session->setFromResponse($response);
    }

    /**
     * @return void
     * @throws \Guilty\Poweroffice\Exceptions\InvalidClientException
     */
    public function getAccessToken()
    {
        $request = $this->client->post($this->getAccessTokenUrl(), [
            'http_errors' => false,
            "auth" => $this->getAuthenticationCredentials(),
            "form_params" => [
                "grant_type" => "client_credentials",
            ],
        ]);

        $response = json_decode($request->getBody(), true);

        if ($request->getStatusCode() === 400 && $response["error"] == "invalid_client") {
            throw new InvalidClientException("The client is invalid");
        }

        $this->session->setFromResponse($response);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @throws \Guilty\PowerOffice\Exceptions\UnauthorizedException
     * @throws \Guilty\PowerOffice\Exceptions\TooManyRequestsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array
     */
    public function performRequest($method, $path, $params = [])
    {
        $options = array_merge([
            'headers' => $this->getAuthorizationHeader(),
            'Accept' => 'application/json',
            'http_errors' => false,
        ], $params);

        /** @var \GuzzleHttp\Psr7\Response $request */
        $request = $this->client->request($method, $this->getApiUrl($path), $options);
        $response = json_decode($request->getBody(), true);

        if ($request->getStatusCode() == 401) {
            throw new UnauthorizedException("The request was denied because you were not authorized");
        }

        if ($request->getStatusCode() == 429) {
            throw new TooManyRequestsException("Too many requests");

        }

        return $response;
    }

    public function createCustomer($params)
    {
        return $this->performRequest("post", "/customer", $params);
    }

    public function getCustomers($params = [])
    {
        return $this->performRequest("get", "/customer", $params);
    }

    public function getCustomer($id)
    {
        return $this->performRequest("get", "/customer/$id");
    }

    public function deleteCustomer($id)
    {
        return $this->performRequest("delete", "/customer/$id");
    }

    public function createOutgoingInvoice($params)
    {
        return $this->performRequest("post", "/outgoinginvoice", $params);
    }

    public function getOutgoingInvoices($params = [])
    {
        return $this->performRequest("get", "/outgoinginvoice/list", $params);
    }

    public function deleteOutgoingInvoice($id)
    {
        return $this->performRequest("delete", "/outgoinginvoice/{$id}");
    }

    public function getOutgoingInvoice($id)
    {
        return $this->performRequest("get", "/outgoinginvoice/$id");
    }

    public function getProducts($params = [])
    {
        return $this->performRequest("get", "/product", $params);
    }

    public function getProduct($id)
    {
        return $this->performRequest("get", "/product/$id");
    }

    public function createProduct($params)
    {
        return $this->performRequest("post", "/product", $params);
    }

    public function deleteProduct($id)
    {
        return $this->performRequest("delete", "/product/$id");
    }

    public function getVatCodes($params = [])
    {
        return $this->performRequest("get", "/vatcode", $params);
    }

    public function getVatCode($id)
    {
        return $this->performRequest("get", "/vatcode/{$id}");
    }

    public function getVatCodeChartOfAccount($vatCode, $params = [])
    {
        return $this->performRequest("get", "VatCode/chartofaccount/{$vatCode}", $params);
    }

    public function getGeneralLedgerAccounts($params = [])
    {
        return $this->performRequest("get", "/GeneralLedgerAccount", $params);
    }

    public function getInvoiceDeliveryTypes()
    {
        return [
            [
                "name" => "None",
                "value" => 0,
                "description" => "No delivery type - error report value only",
            ],
            [
                "name" => "PdfByEmail",
                "value" => 1,
                "description" => "Invoice will be delivered as email with PDF as attachment",
            ],
            [
                "name" => "Print",
                "value" => 2,
                "description" => "Invoice will be printed",
            ],
            [
                "name" => "EHF",
                "value" => 3,
                "description" => "Invoice will be delivered over EHF",
            ],
            [
                "name" => "AvtaleGiro",
                "value" => 4,
                "description" => "The will be delivered over AvtaleGiro",
            ],
            [
                "name" => "External",
                "value" => 5,
                "description" => "The will be delivered over an external third party integration",
            ],
        ];
    }
}