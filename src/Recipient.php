<?php

namespace Anorgan\OnFleet;

/**
 * Class Recipient
 * @package Anorgan\OnFleet
 */
class Recipient extends Entity
{
    protected $name;
    protected $phone;
    protected $notes;
    protected $skipSMSNotifications = false;
    protected $metadata             = [];
    protected $organization;
    protected $timeCreated;
    protected $timeLastModified;

    protected $endpoint = 'recipients';

    protected static $properties = [
        'id',
        'name',
        'phone',
        'notes',
        'skipSMSNotifications',
        'metadata',
        'organization',
        'timeCreated',
        'timeLastModified',
    ];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getNotes()
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
     * @return boolean
     */
    public function isSMSNotificationSkipped(): bool
    {
        return $this->skipSMSNotifications;
    }

    /**
     * @param boolean $state
     */
    public function skipSMSNotifications($state)
    {
        $this->skipSMSNotifications = $state;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
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
    public function delete()
    {
        throw new \BadMethodCallException('Recipient can not be deleted');
    }
}
