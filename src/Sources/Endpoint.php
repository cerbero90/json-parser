<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Concerns\DetectsEndpoints;
use Cerbero\JsonParser\Exceptions\SourceException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Traversable;

/**
 * The endpoint source.
 *
 */
class Endpoint extends Source
{
    use DetectsEndpoints;

    /**
     * The endpoint response.
     *
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        if (!$this->guzzleIsLoaded()) {
            throw SourceException::requireGuzzle();
        }

        $this->response = (new Client())->get($this->source, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        return Psr7Message::from($this->response, $this->config);
    }

    /**
     * Determine whether the Guzzle client is loaded
     *
     * @return bool
     */
    protected function guzzleIsLoaded(): bool
    {
        return class_exists(Client::class);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return is_string($this->source) && $this->isEndpoint($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->response?->getBody()->getSize();
    }
}
