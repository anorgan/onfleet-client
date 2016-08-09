<?php

namespace OnFleet;

class Organization extends Entity
{

    protected $id;
    protected $name;
    protected $timeCreated;
    protected $delegatees = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param mixed $timeCreated
     */
    public function setTimeCreated($timeCreated)
    {
        $this->timeCreated = $timeCreated;
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
     * @param $json
     * @return Organization
     */
    public static function fromJson($json)
    {
        $organization = new static();
        $organization->setId($json->id);
        $organization->setName($json->name);
        $organization->setTimeCreated($json->timeCreated);
        $organization->setDelegatees($json->delegatees);

        return $organization;
    }
}
