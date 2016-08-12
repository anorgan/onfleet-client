<?php

namespace OnFleet;

use GuzzleHttp\Exception\ClientException;

/**
 * Class Entity
 * @package OnFleet
 */
abstract class Entity
{
    protected $id;

    /**
     * @var Client
     */
    protected $client;

    protected $endpoint;
    protected static $properties = [];

    /**
     * Entity constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Entity
     */
    public function update()
    {
        $response = $this->client->put($this->endpoint .'/'. $this->id, [
            'json' => $this->toArray()
        ]);

        static::setProperties($this, $response->json(['object' => true]));

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        try {
            $this->client->delete($this->endpoint .'/'. $this->id);
        } catch (ClientException $e) {
            $error   = $e->getResponse()->json();
            $message = $error['message']['message'];
            if (isset($error['message']['cause'])) {
                $message .= ' '. $error['message']['cause'];
            }
            throw new \Exception('Unable to delete entity: '. $message);
        }

        $this->id = null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach (static::$properties as $property) {
            $array[$property] = $this->$property;
        }
        return $array;
    }

    /**
     * @param $json
     * @param Client $client
     * @return static
     */
    public static function fromJson($json, Client $client)
    {
        $entity = new static($client);
        static::setProperties($entity, $json);

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param $data
     */
    protected static function setProperties(Entity $entity, $data)
    {
        foreach (static::$properties as $property) {
            if (!isset($data->$property)) {
                continue;
            }

            $entity->$property = json_decode(json_encode($data->$property), true);
        }
    }
}
