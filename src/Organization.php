<?php

namespace OnFleet;

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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return array
     */
    public function getDelegatees(): array
    {
        return $this->delegatees;
    }

    /**
     * @param array $delegatees
     */
    public function setDelegatees(array $delegatees)
    {
        $this->delegatees = $delegatees;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @return mixed
     */
    public function getTimeLastModified()
    {
        return $this->timeLastModified;
    }

    /**
     * @throws \BadMethodCallException
     */
    public function update()
    {
        throw new \BadMethodCallException('Organization can not be updated');
    }
}
