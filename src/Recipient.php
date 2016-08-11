<?php

namespace OnFleet;

/**
 * Class Recipient
 * @package OnFleet
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
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
     * @param array $metadata
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
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
    public function delete()
    {
        throw new \BadMethodCallException('Recipient can not be deleted');
    }
}
