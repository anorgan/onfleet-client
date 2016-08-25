<?php

namespace Anorgan\Onfleet;

/**
 * Class Webhook
 * @package Anorgan\Onfleet
 */
class Webhook extends Entity
{
    /**
     * Task started by worker. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_STARTED          = 0;

    /**
     * Worker ETA less than or equal to notification threshold. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_ETA              = 1;

    /**
     * Worker arriving, 150 meters away or closer. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_ARRIVAL          = 2;

    /**
     * Task completed by worker. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_COMPLETED        = 3;

    /**
     * Task failed. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_FAILED           = 4;

    /**
     * New task created. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_CREATED          = 6;

    /**
     * Task updated, including assignment changes. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_UPDATED          = 7;

    /**
     * Task deleted. Request includes: taskId
     */
    const TRIGGER_TASK_DELETED          = 8;

    /**
     * Task assigned to worker. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_ASSIGNED         = 9;

    /**
     * Task unassigned from worker. Request includes: taskId, data.task
     */
    const TRIGGER_TASK_UNASSIGNED       = 10;

    /**
     * Worker status changed (0 for off-duty, 1 for on-duty). Request includes: workerId, status, data.worker
     */
    const TRIGGER_WORKER_DUTY           = 5;

    protected $url;
    protected $trigger;
    protected $count;

    protected $endpoint = 'webhooks';

    protected static $properties = [
        'id',
        'url',
        'trigger',
        'count',
    ];

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Total number of successful requests made for a webhook.
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param array $metadata
     * @internal
     */
    public function setMetadata(array $metadata)
    {
        throw new \BadMethodCallException('Webhook does not support metadata');
    }
}
