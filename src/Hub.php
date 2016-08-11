<?php

namespace OnFleet;

/**
 * Class Hub
 * @package OnFleet
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
}
