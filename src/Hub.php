<?php

namespace Onfleet;

/**
 * Class Hub
 * @package Onfleet
 */
class Hub extends Entity
{
    protected $name;
    protected $location;
    protected $address;

    protected $endpoint = 'hubs';

    protected static $properties = [
        'id',
        'name',
        'location',
        'address',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @throws \BadMethodCallException
     */
    public function update()
    {
        throw new \BadMethodCallException('Hub can not be updated');
    }

    /**
     * @throws \BadMethodCallException
     */
    public function delete()
    {
        throw new \BadMethodCallException('Hub can not be deleted');
    }

    /**
     * @param array $metadata
     * @internal
     */
    public function setMetadata(array $metadata)
    {
        throw new \BadMethodCallException('Hub does not support metadata');
    }
}

