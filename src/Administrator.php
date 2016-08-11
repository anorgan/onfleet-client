<?php

namespace OnFleet;

class Administrator extends Entity
{
    const TYPE_CREATOR    = 'super';
    const TYPE_DISPATCHER = 'standard';

    protected $name;
    protected $email;
    protected $phone;
    protected $type;
    protected $isActive;
    protected $timeCreated;
    protected $timeLastModified;
    protected $organization;
    protected $metadata = [];

    protected $endpoint = 'admins';

    protected static $properties = [
        'id',
        'name',
        'email',
        'phone',
        'isActive',
        'organization',
        'type',
        'metadata',
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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
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
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
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
}
