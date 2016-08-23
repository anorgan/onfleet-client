<?php

namespace Anorgan\OnFleet;

/**
 * Class Worker
 * @package Anorgan\OnFleet
 */
class Worker extends Entity
{
    const TYPE_CREATOR    = 'super';
    const TYPE_DISPATCHER = 'standard';

    protected $name;
    protected $phone;
    protected $activeTask;
    protected $tasks = [];
    protected $onDuty;
    protected $location;
    protected $timeLastSeen;
    protected $delayTime;
    protected $teams = [];
    protected $organization;
    protected $metadata = [];
    protected $analytics;
    protected $vehicle;
    protected $timeCreated;
    protected $timeLastModified;

    protected $endpoint = 'workers';

    protected static $properties = [
        'id',
        'name',
        'phone',
        'activeTask',
        'tasks',
        'onDuty',
        'location',
        'timeLastSeen',
        'delayTime',
        'teams',
        'organization',
        'vehicle',
        'metadata',
        'analytics',
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
     * @return mixed
     */
    public function getActiveTask()
    {
        return $this->activeTask;
    }

    /**
     * @param mixed $activeTask
     */
    public function setActiveTask($activeTask)
    {
        $this->activeTask = $activeTask;
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @param array $tasks
     */
    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return bool
     */
    public function isOnDuty()
    {
        return $this->onDuty;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return \DateTime
     */
    public function getTimeLastSeen()
    {
        return $this->toDateTime($this->timeLastSeen);
    }

    /**
     * @return int Amount of time in seconds that a worker is delayed by.
     */
    public function getDelayTime()
    {
        return $this->delayTime;
    }

    /**
     * @return array
     */
    public function getTeams(): array
    {
        return $this->teams;
    }

    /**
     * @param array $teams
     */
    public function setTeams(array $teams)
    {
        $this->teams = $teams;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
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
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @return mixed
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
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
}
