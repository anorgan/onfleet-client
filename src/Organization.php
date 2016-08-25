<?php

namespace Anorgan\Onfleet;

/**
 * Class Organization
 * @package Anorgan\Onfleet
 */
class Organization extends Entity
{
    protected $name;
    protected $email;
    protected $image;
    protected $timezone;
    protected $country;
    protected $timeCreated;
    protected $timeLastModified;
    protected $delegatees = [];

    protected $endpoint = 'organization';

    protected static $properties = [
        'id',
        'name',
        'email',
        'image',
        'timezone',
        'country',
        'timeCreated',
        'timeLastModified',
        'delegatees',
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return array
     */
    public function getDelegatees(): array
    {
        return $this->delegatees;
    }

    /**
     * @return \DateTime
     */
    public function getTimeCreated()
    {
        return $this->toDateTime($this->timeCreated);
    }

    /**
     * @return \DateTime
     */
    public function getTimeLastModified()
    {
        return $this->toDateTime($this->timeLastModified);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function update()
    {
        throw new \BadMethodCallException('Organization can not be updated');
    }

    /**
     * @param array $metadata
     * @internal
     */
    public function setMetadata(array $metadata)
    {
        throw new \BadMethodCallException('Organization does not support metadata');
    }
}
