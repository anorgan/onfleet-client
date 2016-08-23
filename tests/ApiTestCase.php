<?php

namespace OnFleet\Tests;

use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use OnFleet\Client;
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
     * @var Mock
     */
    protected $mockedResponses;

    /**
     * @var History
     */
    protected $history;

    /**
     * Setup client with history and mocked responses
     */
    public function setUp()
    {
        $this->client = new Client(null);
        $this->mockedResponses = new Mock();
        $this->history = new History();

        $this->client->getEmitter()->attach($this->history);
        $this->client->getEmitter()->attach($this->mockedResponses);
    }

    /**
     * @param string $path
     */
    public function assertRequestIsGet($path)
    {
        $request = $this->history->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());

        $this->assertEquals($this->baseUrl . $path, $request->getUrl());
    }

    /**
     * Assert request is post, has JSON content type and optionally check payload data
     *
     * @param string $path
     * @param array|null $data
     */
    public function assertRequestIsPost($path, array $data = null)
    {
        $request = $this->history->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeader('Content-type'));

        $this->assertEquals($this->baseUrl . $path, $request->getUrl());

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
        $request = $this->history->getLastRequest();
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeader('Content-type'));

        $this->assertEquals($this->baseUrl . $path, $request->getUrl());

        if (null !== $data) {
            $payload = $request->getBody()->__toString();
            $this->assertJsonStringEqualsJsonString(json_encode($data), $payload);
        }
    }
}
