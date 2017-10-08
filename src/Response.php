<?php

namespace Anorgan\Onfleet;

use GuzzleHttp\Psr7\Response as BaseResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 * @package Anorgan\Onfleet
 */
class Response extends BaseResponse
{
    /**
     * @param ResponseInterface $response
     * @return Response
     */
    public static function fromResponse(ResponseInterface $response): Response
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase());
    }

    /**
     * @param bool $array
     * @return array|null|object
     */
    public function json(bool $array = false)
    {
        return json_decode($this->getBody(), $array);
    }
}
