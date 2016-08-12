<?php

namespace OnFleet;

/**
 * Class Destination
 * @package OnFleet
 */
class Destination extends Entity
{
    protected $address  = [];
    protected $location = [];
    protected $notes;
    protected $metadata = [];
    protected $timeCreated;
    protected $timeLastModified;

    protected $endpoint = 'destinations';

    protected static $properties = [
        'id',
        'address',
        'location',
        'notes',
        'metadata',
        'timeCreated',
        'timeLastModified',
    ];

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    /**
     * @param array $address
     */
    public function setAddress(array $address)
    {
        $this->address = $address;
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->location;
    }

    /**
     * @param array $location
     */
    public function setLocation(array $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
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
        throw new \BadMethodCallException('Destination can not be updated');
    }

    /**
     * @throws \BadMethodCallException
     */
    public function delete()
    {
        throw new \BadMethodCallException('Destination can not be deleted');
    }
}
