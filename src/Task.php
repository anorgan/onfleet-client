<?php

namespace Anorgan\Onfleet;

use Symfony\Component\Console\Exception\LogicException;

/**
 * Class Task
 * @package Anorgan\Onfleet
 */
class Task extends Entity
{
    /**
     * Unassigned: task has not yet been assigned to a worker.
     */
    const STATE_UNASSIGNED = 0;

    /**
     * Assigned: task has been assigned to a worker.
     */
    const STATE_ASSIGNED = 1;

    /**
     * Active: task has been started by its assigned worker.
     */
    const STATE_ACTIVE = 2;

    /**
     * Completed: task has been completed by its assigned worker.
     */
    const STATE_COMPLETED = 3;

    /**
     * The distance mode finds the active worker whose last-known location is closest to the task's destination.
     */
    const AUTO_ASSIGN_MODE_DISTANCE = 'distance';

    /**
     * The worker who has to travel the shortest distance is considered to have the lightest load,
     * and as such will be assigned the task being created.
     */
    const AUTO_ASSIGN_MODE_LOAD = 'load';

    const CONTAINER_TYPE_TEAM   = 'TEAM';
    const CONTAINER_TYPE_WORKER = 'WORKER';

    protected $organization;
    protected $shortId;
    protected $trackingURL;
    protected $worker;
    protected $merchant;
    protected $executor;
    protected $creator;
    protected $dependencies = [];
    protected $state        = self::STATE_UNASSIGNED;
    protected $completeAfter;
    protected $completeBefore;
    protected $pickupTask = false;
    protected $notes;
    protected $completionDetails = [];
    protected $feedback          = [];
    protected $metadata          = [];
    protected $overrides         = [];
    protected $container         = [];
    protected $recipients        = [];
    protected $destination;
    protected $delayTime;
    protected $timeCreated;
    protected $timeLastModified;
    protected $didAutoAssign;

    protected $endpoint = 'tasks';

    protected static $properties = [
        'id',
        'organization',
        'shortId',
        'trackingURL',
        'worker',
        'merchant',
        'executor',
        'creator',
        'dependencies',
        'state',
        'completeAfter',
        'completeBefore',
        'pickupTask',
        'notes',
        'completionDetails',
        'feedback',
        'metadata',
        'overrides',
        'container',
        'recipients',
        'destination',
        'delayTime',
        'timeCreated',
        'timeLastModified',
        'didAutoAssign',
    ];

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string
     */
    public function getShortId()
    {
        return $this->shortId;
    }

    /**
     * @return string
     */
    public function getTrackingURL()
    {
        return $this->trackingURL;
    }

    /**
     * @return string ID of the worker. This properly will be completely removed from non-completed tasks in favor of
     *                                  container in future versions of the API.
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return string|null
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @param string $merchant
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * @return string|null
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @param string $executor
     */
    public function setExecutor($executor)
    {
        $this->executor = $executor;
    }

    /**
     * @return string ID of creator
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies = null)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState(int $state)
    {
        $this->state = $state;
    }

    /**
     * @return int Amount of time in seconds that a task is delayed by.
     */
    public function getDelayTime()
    {
        return $this->delayTime;
    }

    /**
     * @return \DateTime
     */
    public function getCompleteAfter()
    {
        return $this->toDateTime($this->completeAfter);
    }

    /**
     * @param int|\DateTime $completeAfter
     */
    public function setCompleteAfter($completeAfter)
    {
        $this->completeAfter = $this->toTimestamp($completeAfter);
    }

    /**
     * @return \DateTime
     */
    public function getCompleteBefore()
    {
        return $this->toDateTime($this->completeBefore);
    }

    /**
     * @param int|\DateTime $completeBefore
     */
    public function setCompleteBefore($completeBefore)
    {
        $this->completeBefore = $this->toTimestamp($completeBefore);
    }

    /**
     * @return boolean
     */
    public function isPickupTask(): bool
    {
        return $this->pickupTask;
    }

    /**
     * @param boolean $pickupTask
     */
    public function setPickupTask($pickupTask)
    {
        $this->pickupTask = $pickupTask;
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
     * @return array
     */
    public function getCompletionDetails(): array
    {
        return $this->completionDetails;
    }

    /**
     * @param array $completionDetails
     */
    public function setCompletionDetails(array $completionDetails)
    {
        $this->completionDetails = $completionDetails;
    }

    /**
     * @return array
     */
    public function getFeedback(): array
    {
        return $this->feedback;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function getOverrides(): array
    {
        return $this->overrides;
    }

    /**
     * @param array $overrides
     */
    public function setOverrides(array $overrides)
    {
        $this->overrides = $overrides;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        return $this->container;
    }

    /**
     * @param array $container
     */
    public function setContainer(array $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
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
     * Available only on creation, and if using auto assign feature. If we fail to find a suitable worker because of an
     * error or any other unexpected condition, didAutoAssign will be false.
     * @return boolean
     */
    public function isAutoAssigned()
    {
        return $this->didAutoAssign;
    }

    /**
     * @throws \LogicException
     * @return Entity
     */
    public function update()
    {
        if (self::STATE_COMPLETED === $this->getState()) {
            throw new LogicException('Unable to modify completed task');
        }

        return parent::update();
    }

    /**
     * @throws \LogicException
     */
    public function delete()
    {
        if (self::STATE_COMPLETED === $this->getState()) {
            throw new LogicException('Unable to delete completed task');
        }

        if (self::STATE_ACTIVE === $this->getState()) {
            throw new LogicException('Unable to delete active task');
        }

        parent::delete();
    }

    /**
     * @param bool $success
     * @param string $notes
     */
    public function complete($success = true, $notes = null)
    {
        if (self::STATE_ACTIVE !== $this->getState()) {
            throw new LogicException('Unable to complete non active task');
        }
        $this->client->post($this->endpoint .'/'. $this->id .'/complete', [
            'json' => [
                'completionDetails' => [
                    'success' => $success,
                    'notes'   => $notes
                ]
            ]
        ]);

        $this->state = self::STATE_COMPLETED;
    }


    /**
     * @param Destination $destination           The valid Destination object.
     * @param Recipient|Recipient[] $recipients  A valid array of zero or one Recipient objects.
     * @param Organization $merchant             Optional. The organization that will be displayed to the
     *                                           recipient of the task. Defaults to the creating organization.
     *                                           If you perform deliveries on behalf of a connected organization and
     *                                           want to display their name, logo, and branded notifications,
     *                                           provide their organization.
     * @param Organization $executor             Optional. The organization that will be responsible for fulfilling the
     *                                           task. Defaults to the creating organization. If you delegate your
     *                                           deliveries to a third party, provide their organization.
     * @param string $notes                        Optional. Notes for the task, e.g. "Order 332: 10 x Soup de Jour"
     * @param string $autoAssignMode             The desired automatic assignment mode. Either distance or load.
     * @param Team $autoAssignTeam               Optional. The team from which to pick the workers to consider for
     *                                           automatic assignment.
     * @param int|\DateTime $completeAfter                 Optional. A timestamp for the earliest time the task should be completed.
     * @param int|\DateTime $completeBefore                Optional. A timestamp for the latest time the task should be completed.
     * @param bool $pickupTask                   Optional. Whether the task is a pickup task.
     * @param array|null $dependencies           Optional. One or more IDs of tasks which must be completed prior to this task.
     * @param int $quantity                      Optional. The number of units to be dropped off while completing this
     *                                           task, for route optimization purposes.
     * @param int $serviceTime                   Optional. The number of minutes to be spent by the worker on arrival at
     *                                           this task's destination, for route optimization purposes.
     * @return array
     */
    public static function createAutoAssignedArray(
        Destination $destination,
        $recipients,
        Organization $merchant = null,
        Organization $executor = null,
        $notes = null,
        $autoAssignMode = self::AUTO_ASSIGN_MODE_LOAD,
        Team $autoAssignTeam = null,
        $completeAfter = null,
        $completeBefore = null,
        $pickupTask = false,
        $dependencies = null,
        $quantity = null,
        $serviceTime = null
    ): array {
        $task = new static(new Client(null));

        if ($recipients instanceof Recipient) {
            $recipients = [$recipients->getId()];
        }
        $task->setRecipients($recipients);

        if ($destination instanceof Destination) {
            $destination = $destination->getId();
        }
        $task->setDestination($destination);

        if (null !== $merchant) {
            $task->setMerchant($merchant->getId());
        }

        if (null !== $executor) {
            $task->setExecutor($executor->getId());
        }

        $task->setNotes($notes);
        $task->setCompleteAfter($completeAfter);
        $task->setCompleteBefore($completeBefore);
        $task->setPickupTask($pickupTask);
        $task->setDependencies($dependencies);

        $taskArray = $task->toArray();

        $taskArray['quantity']    = $quantity;
        $taskArray['serviceTime'] = $serviceTime;

        $taskArray['autoAssign'] = [
            'mode' => $autoAssignMode
        ];

        if (null !== $autoAssignTeam) {
            $taskArray['autoAssign']['team'] = $autoAssignTeam->getId();
        }

        return array_filter($taskArray);
    }

    /**
     * @param Destination $destination           The valid Destination object.
     * @param Recipient|Recipient[] $recipients  A valid array of zero or one Recipient objects.
     * @param Organization $merchant             Optional. The organization that will be displayed to the
     *                                           recipient of the task. Defaults to the creating organization.
     *                                           If you perform deliveries on behalf of a connected organization and
     *                                           want to display their name, logo, and branded notifications,
     *                                           provide their organization.
     * @param Organization $executor             Optional. The organization that will be responsible for fulfilling the
     *                                           task. Defaults to the creating organization. If you delegate your
     *                                           deliveries to a third party, provide their organization.
     * @param string $notes                        Optional. Notes for the task, e.g. "Order 332: 10 x Soup de Jour"
     * @param string $containerType              The type of the container to which the task is to be assigned.
     * @param string $containerTarget            ID of the target team or worker.
     * @param int $completeAfter                 Optional. A timestamp for the earliest time the task should be completed.
     * @param int $completeBefore                Optional. A timestamp for the latest time the task should be completed.
     * @param bool $pickupTask                   Optional. Whether the task is a pickup task.
     * @param array $dependencies                Optional. One or more IDs of tasks which must be completed prior to this task.
     * @param int $quantity                      Optional. The number of units to be dropped off while completing this
     *                                           task, for route optimization purposes.
     * @param int $serviceTime                   Optional. The number of minutes to be spent by the worker on arrival at
     *                                           this task's destination, for route optimization purposes.
     * @return array
     */
    public static function createManualAssignedArray(
        Destination $destination,
        $recipients,
        Organization $merchant = null,
        Organization $executor = null,
        $notes = null,
        $containerType = self::CONTAINER_TYPE_TEAM,
        $containerTarget,
        $completeAfter = null,
        $completeBefore = null,
        $pickupTask = false,
        $dependencies = null,
        $quantity = null,
        $serviceTime = null
    ): array {
        $task = new static(new Client(null));

        if ($recipients instanceof Recipient) {
            $recipients = [$recipients->getId()];
        }
        $task->setRecipients($recipients);

        if ($destination instanceof Destination) {
            $destination = $destination->getId();
        }
        $task->setDestination($destination);

        if (null !== $merchant) {
            $task->setMerchant($merchant->getId());
        }

        if (null !== $executor) {
            $task->setExecutor($executor->getId());
        }

        $task->setNotes($notes);
        $task->setCompleteAfter($completeAfter);
        $task->setCompleteBefore($completeBefore);
        $task->setPickupTask($pickupTask);
        $task->setDependencies($dependencies);

        $taskArray = $task->toArray();

        $taskArray['quantity']    = $quantity;
        $taskArray['serviceTime'] = $serviceTime;

        $taskArray['container'] = [
            'type'                     => $containerType,
            strtolower($containerType) => $containerTarget
        ];

        return array_filter($taskArray);
    }
}
