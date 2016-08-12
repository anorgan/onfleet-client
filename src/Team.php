<?php

namespace OnFleet;

/**
 * Class Team
 * @package OnFleet
 */
class Team extends Entity
{
    protected $name;
    protected $workers  = [];
    protected $managers = [];
    protected $tasks    = [];
    protected $hub;
    protected $timeCreated;
    protected $timeLastModified;

    protected $endpoint = 'teams';

    protected static $properties = [
        'id',
        'name',
        'workers',
        'managers',
        'tasks',
        'hub',
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
     * @return array
     */
    public function getWorkers(): array
    {
        return $this->workers;
    }

    /**
     * @param array $workers
     */
    public function setWorkers(array $workers)
    {
        $this->workers = $workers;
    }

    /**
     * @return array
     */
    public function getManagers(): array
    {
        return $this->managers;
    }

    /**
     * @param array $managers
     */
    public function setManagers(array $managers)
    {
        $this->managers = $managers;
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @return string
     */
    public function getHub()
    {
        return $this->hub;
    }

    /**
     * @param string $hub
     */
    public function setHub($hub)
    {
        $this->hub = $hub;
    }

    /**
     * @param array $metadata
     * @internal
     */
    public function setMetadata(array $metadata)
    {
        throw new \BadMethodCallException('Team does not support metadata');
    }
}

