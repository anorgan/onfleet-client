<?php

namespace Anorgan\Onfleet\Tests;

use Anorgan\Onfleet\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

class ApiTestCase extends TestCase
{
    /**
     * Base URL for all paths to append to
     *
     * @var string
     */
    protected $baseUrl = 'https://onfleet.com/api/v2/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var MockHandler
     */
    protected $mockedResponses;

    /**
     * @var callable
     */
    protected $history;

    /**
     * Setup client with history and mocked responses
     */
    public function setUp()
    {
        $historyContainer      = [];
        $this->history         = Middleware::history($historyContainer);
        $this->mockedResponses = new MockHandler();
        $stack                 = HandlerStack::create($this->mockedResponses);
        // Add the history middleware to the handler stack.
        $stack->push($this->history);

        $this->client          = new Client(null, ['handler' => $stack]);
    }

    /**
     * @param string $path
     */
    public function assertRequestIsGet($path)
    {
        $request = $this->mockedResponses->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());

        $this->assertEquals($this->baseUrl . $path, $request->getUri());
    }

    /**
     * Assert request is post, has JSON content type and optionally check payload data
     *
     * @param string $path
     * @param array|null $data
     */
    public function assertRequestIsPost($path, array $data = null)
    {
        $request = $this->mockedResponses->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeader('Content-type')[0]);

        $this->assertEquals($this->baseUrl . $path, $request->getUri());

        if (null !== $data) {
            $payload = $request->getBody()->__toString();
            $this->assertJsonStringEqualsJsonString(json_encode($data), $payload);
        }
    }

    /**
     * Assert request is put, has JSON content type and optionally check payload data
     *
     * @param string $path
     * @param array|null $data
     */
    public function assertRequestIsPut($path, array $data = null)
    {
        $request = $this->mockedResponses->getLastRequest();
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeader('Content-type')[0]);

        $this->assertEquals($this->baseUrl . $path, $request->getUri());

        if (null !== $data) {
            $payload = $request->getBody()->__toString();
            $this->assertJsonStringEqualsJsonString(json_encode($data), $payload);
        }
    }
}
