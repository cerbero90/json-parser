<?php

namespace Cerbero\JsonParser\Concerns;

use Cerbero\JsonParser\Exceptions\GuzzleRequiredException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * The Guzzle-aware trait.
 *
 */
trait GuzzleAware
{
    /**
     * Abort if Guzzle is not loaded
     *
     * @return void
     * @throws GuzzleRequiredException
     */
    protected function requireGuzzle(): void
    {
        if (!$this->guzzleIsInstalled()) {
            throw new GuzzleRequiredException();
        }
    }

    /**
     * Determine whether Guzzle is installed
     *
     * @return bool
     */
    protected function guzzleIsInstalled(): bool
    {
        return class_exists(Client::class);
    }

    /**
     * Retrieve the JSON response of the given URL
     *
     * @param UriInterface|string $url
     * @return ResponseInterface
     */
    protected function getJson(UriInterface|string $url): ResponseInterface
    {
        return $this->guzzle()->get($url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Retrieve the Guzzle client
     *
     * @codeCoverageIgnore
     * @return Client
     */
    protected function guzzle(): Client
    {
        return new Client();
    }

    /**
     * Retrieve the JSON response of the given request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->guzzle()->sendRequest($request);
    }
}
