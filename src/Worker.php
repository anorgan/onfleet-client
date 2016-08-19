<?php

namespace OnFleet;

/**
 * Class Worker
 * @package OnFleet
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
     * @return mixed
     */
    public function getOnDuty()
    {
        return $this->onDuty;
    }

    /**
     * @param mixed $onDuty
     */
    public function setOnDuty($onDuty)
    {
        $this->onDuty = $onDuty;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getTimeLastSeen()
    {
        return $this->timeLastSeen;
    }

    /**
     * @param mixed $timeLastSeen
     */
    public function setTimeLastSeen($timeLastSeen)
    {
        $this->timeLastSeen = $timeLastSeen;
    }

    /**
     * @return mixed
     */
    public function getDelayTime()
    {
        return $this->delayTime;
    }

    /**
     * @param mixed $delayTime
     */
    public function setDelayTime($delayTime)
    {
        $this->delayTime = $delayTime;
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
