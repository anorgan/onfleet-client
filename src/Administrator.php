<?php

namespace OnFleet;

class Administrator extends Entity
{
    const TYPE_CREATOR    = 'super';
    const TYPE_DISPATCHER = 'standard';

    protected $id;
    protected $name;
    protected $email;
    protected $phone;
    protected $type;
    protected $isActive;
    protected $timeCreated;
    protected $timeLastModified;
    protected $organization;
    protected $metadata = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

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

    /**
     * @param $json
     * @return Administrator
     */
    public static function fromJson($json)
    {
        $administrator                   = new static();
        $administrator->id               = $json->id;
        $administrator->name             = $json->name;
        $administrator->email            = $json->email;
        $administrator->phone            = $json->phone;
        $administrator->isActive         = $json->isActive;
        $administrator->organization     = $json->organization;
        $administrator->type             = $json->type;
        $administrator->timeCreated      = $json->timeCreated;
        $administrator->timeLastModified = $json->timeCreated;
        $administrator->metadata         = $json->metadata;

        return $administrator;
    }
}
