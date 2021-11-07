<?php
declare(strict_types=1);

namespace Dathard\Base\Service;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;

class GitApiService
{
    const API_REQUEST_URI = 'https://api.github.com/';

    const API_REQUEST_ENDPOINT = 'repos/';

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $repositoryName
     * @return null|array
     */
    public function getReleasesList(string $repositoryName)
    {
        $uriEndpoint = self::API_REQUEST_URI . static::API_REQUEST_ENDPOINT . $repositoryName . '/releases';
        $response = $this->doRequest($uriEndpoint);

        if ($response->getStatusCode() != 200) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $repositoryName
     * @return null|array
     */
    public function getLatestRelease(string $repositoryName)
    {
        $uriEndpoint = static::API_REQUEST_ENDPOINT . $repositoryName . '/releases/latest';
        $response = $this->doRequest($uriEndpoint);

        if ($response->getStatusCode() != 200) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        /** @var ClientFactory $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var ResponseFactory $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
