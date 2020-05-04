<?php

namespace Anorgan\Onfleet;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Class Client
 * @package Anorgan\Onfleet
 */
class Client extends Guzzle
{
    const BASE_URL = 'https://onfleet.com/api/v2/';
    const VERSION  = 'v2';

    /**
     * Client constructor.
     * @param string $username
     * @param array $config
     */
    public function __construct($username, array $config = [])
    {
        $baseUriKey = version_compare(ClientInterface::VERSION, '6') === 1 ? 'base_uri' : 'base_url';
        if (!isset($config[$baseUriKey])) {
            $config[$baseUriKey] = static::BASE_URL;
        }
        $config['auth'] = [$username, null];

        parent::__construct($config);
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @throws \Exception
     * @return ResponseInterface|Response
     */
    public function post($uri, array $options = [])
    {
        try {
            $response = parent::post($uri, $options);
            return Response::fromResponse($response);
        } catch (ClientException $e) {
            $error   = Response::fromResponse($e->getResponse())->json(true);
            $message = $error['message']['message'];
            if (isset($error['message']['cause'])) {
                if (is_array($error['message']['cause'])) {
                    $message .= ' '.implode(', ', $error['message']['cause']);
                } else {
                    $message .= ' '.$error['message']['cause'];
                }
            }
            throw new RuntimeException('Error while calling post on '.$uri.': '.$message);
        }
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @throws \GuzzleHttp\Exception\ClientException
     * @return null|\Psr\Http\Message\ResponseInterface|Response
     */
    public function get($uri, array $options = [])
    {
        try {
            return Response::fromResponse(parent::get($uri, $options));
        } catch (ClientException $e) {
            if ((int) $e->getCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * @return Organization
     */
    public function getMyOrganization(): Organization
    {
        $response = $this->get('organization');
        return Organization::fromJson($response->json(), $this);
    }

    /**
     * Get delegatee organization
     *
     * @param string $id
     * @return Organization
     */
    public function getOrganization($id): Organization
    {
        $response = $this->get('organizations/'.$id);
        return Organization::fromJson($response->json(), $this);
    }

    public function getTaskDetails($onfleetTaskId)
    {
        $response = $this->get('tasks/' . $onfleetTaskId);
		
		$details = $response ? json_encode($response->json()) : ['error' => 'not found'];

        return $details;
    }
	
	public function getTaskDetailsShort($onfleetTaskIdShort)
    {
        $response = $this->get('tasks/shortId/' . $onfleetTaskIdShort);
		
		$details = $response ? json_encode($response->json()) : ['error' => 'not found'];

        return $details;
    }

    public function getDriverDetails($workerId)
    {
        $response = $this->get('workers/' . $workerId);

        $details = json_decode(json_encode($response->json()), true);

        return $details;
    }
	
	public function completeTask($onfleetTaskId)
    {
        //pre($this->getTaskDetails($onfleetTaskId), 1);

        $data = [
            'completionDetails' => [
                'success' => $onfleetTaskId ? true : false,
            ]
        ];

        $this->post('tasks/' . $onfleetTaskId . '/complete', ['json' => $data]);
    }

    /**
     * @param array $data {
     * @var string $name The administrator’s complete name.
     * @var string $email The administrator’s email address.
     * @var string $phone Optional. The administrator’s phone number.
     * @var boolean $isReadOnly Optional. Whether this administrator can perform write operations.
     * }
     * @return Administrator
     */
    public function createAdministrator(array $data): Administrator
    {
        $response = $this->post('admins', ['json' => $data]);
        return Administrator::fromJson($response->json(), $this);
    }

    /**
     * @return Administrator[]
     */
    public function getAdministrators(): array
    {
        $response = $this->get('admins');

        $administrators = [];
        foreach ($response->json() as $administratorData) {
            $administrators[] = Administrator::fromJson($administratorData, $this);
        }

        return $administrators;
    }

    /**
     * @param array $data {
     * @var string $name The worker’s complete name.
     * @var string $phone A valid phone number as per the worker’s organization’s country.
     * @var string|array $teams One or more team IDs of which the worker is a member.
     * @var object $vehicle Optional. The worker’s vehicle; providing no vehicle details is equivalent to the
     *                              worker being on foot.
     * @var string $type The vehicle’s type, must be one of CAR, MOTORCYCLE, BICYCLE or TRUCK.
     * @var string $description Optional. The vehicle’s make, model, year, or any other relevant identifying details.
     * @var string $licensePlate Optional. The vehicle’s license plate number.
     * @var string $color Optional. The vehicle's color.
     * @var integer $capacity Optional. The maximum number of units this worker can carry, for route optimization purposes.
     * }
     * @return Worker
     */
    public function createWorker(array $data): Worker
    {
        $response = $this->post('workers', ['json' => $data]);
        return Worker::fromJson($response->json(), $this);
    }

    /**
     * @param string $filter Optional. A comma-separated list of fields to return, if all are not desired. For example, name, location.
     * @param string $teams Optional. A comma-separated list of the team IDs that workers must be part of.
     * @param string $states Optional. A comma-separated list of worker states, where
     *                       0 is off-duty, 1 is idle (on-duty, no active task) and 2 is active (on-duty, active task).
     * @return Worker[]
     */
    public function getWorkers($filter = null, $teams = null, $states = null): array
    {
        $query = array_filter([
            'filter' => $filter,
            'teams'  => $teams,
            'states' => $states,
        ]);
        $response = $this->get('workers', compact('query'));

        $workers = [];
        foreach ($response->json() as $workerData) {
            $workers[] = Worker::fromJson($workerData, $this);
        }

        return $workers;
    }

    /**
     * @param string $id
     * @param string $filter Optional. A comma-separated list of fields to return, if all are not desired.
     *                        For example: "name, location".
     * @param bool $analytics Basic worker duty event, traveled distance (meters) and time analytics are optionally
     *                        available by specifying the query parameter analytics as true.
     * @return Worker
     *
     * @todo: Add "from" and "to" timestamps if analytics is true
     */
    public function getWorker($id, $filter = null, $analytics = false): Worker
    {
        $query = array_filter([
            'filter'    => $filter,
            'analytics' => $analytics ? 'true' : 'false',
        ]);
        $response = $this->get('workers/'.$id, compact('query'));

        return Worker::fromJson($response->json(), $this);
    }

    /**
     * @return Hub[]
     */
    public function getHubs(): array
    {
        $response = $this->get('hubs');

        $hubs = [];
        foreach ($response->json() as $hubData) {
            $hubs[] = Hub::fromJson($hubData, $this);
        }

        return $hubs;
    }

    /**
     * @param array $data {
     * @var string $name A unique name for the team.
     * @var array $workers An array of worker IDs.
     * @var array $managers An array of managing administrator IDs.
     * @var string $hub Optional. The ID of the team's hub.
     * }
     *
     * @return Team
     */
    public function createTeam(array $data): Team
    {
        $response = $this->post('teams', ['json' => $data]);
        return Team::fromJson($response->json(), $this);
    }

    /**
     * @return Team[]
     */
    public function getTeams(): array
    {
        $response = $this->get('teams');

        $teams = [];
        foreach ($response->json() as $teamData) {
            $teams[] = Team::fromJson($teamData, $this);
        }

        return $teams;
    }

    /**
     * @param string $id
     * @return Team
     */
    public function getTeam($id): Team
    {
        $response = $this->get('teams/'.$id);
        return Team::fromJson($response->json(), $this);
    }

    /**
     * @param array $data {
     * @var array $address The destination’s street address details. {
     * @var string $name Optional. A name associated with this address, e.g., "Transamerica Pyramid".
     * @var string $number The number component of this address, it may also contain letters.
     * @var string $street The name of the street.
     * @var string $apartment Optional. The suite or apartment number, or any additional relevant information.
     * @var string $city The name of the municipality.
     * @var string $state Optional. The name of the state, province or jurisdiction.
     * @var string $postalCode Optional. The postal or zip code.
     * @var string $country The name of the country.
     * @var string $unparsed Optional. A complete address specified in a single, unparsed string where the
     *                                 various elements are separated by commas. If present, all other address
     *                                 properties will be ignored (with the exception of name and apartment).
     *                                 In some countries, you may skip most address details (like city or state)
     *                                 if you provide a valid postalCode: for example,
     *                                 543 Howard St, 94105, USA will be geocoded correctly.
     *     }
     * @var string $notes Optional. Note that goes with this destination, e.g. "Please call before"
     * @var array $location Optional. The [ longitude, latitude ] geographic coordinates. If missing, the API will
     *                          geocode based on the address details provided. Note that geocoding may slightly modify
     *                          the format of the address properties. address.unparsed cannot be provided if you are
     *                          also including a location.
     * }
     * @return Destination
     */
    public function createDestination(array $data): Destination
    {
        $response = $this->post('destinations', ['json' => $data]);
        return Destination::fromJson($response->json(), $this);
    }

    /**
     * @param string $id
     * @return Destination
     */
    public function getDestination($id): Destination
    {
        $response = $this->get('destinations/'.$id);
        return Destination::fromJson($response->json(), $this);
    }

    /**
     * @param array $data {
     * @var string $name The recipient’s complete name.
     * @var string $phone A unique, valid phone number as per the recipient’s organization’s country.
     * @var string $notes Optional. Notes for this recipient: these are global notes that should not be
     *                          task- or destination-specific.
     *                          For example, "Customer since June 2012, does not drink non-specialty coffee".
     * @var boolean $skipSMSNotifications Optional. Whether this recipient has requested to not receive SMS
     *                          notifications. Defaults to false if not provided.
     * @var boolean $skipPhoneNumberValidation Optional. Whether to skip validation of this recipient's phone
     *                          number. An E.164-like number is still required (must start with +), however the API
     *                          will not enforce any country-specific validation rules.
     * }
     * @return Recipient
     */
    public function createRecipient(array $data): Recipient
    {
        $response = $this->post('recipients', ['json' => $data]);
        return Recipient::fromJson($response->json(), $this);
    }

    /**
     * @param string $id
     * @return Recipient
     */
    public function getRecipient($id): Recipient
    {
        $response = $this->get('recipients/'.$id);
        return Recipient::fromJson($response->json(), $this);
    }

    /**
     * @param string $name
     * @return Recipient|null
     */
    public function getRecipientByName($name)
    {
        $name     = str_replace('+', '%20', urlencode(strtolower($name)));
        $response = $this->get('recipients/name/'.$name);

        if (null === $response) {
            return null;
        }

        return Recipient::fromJson($response->json(), $this);
    }

    /**
     * @param string $phone
     * @return Recipient|null
     */
    public function getRecipientByPhone($phone)
    {
        $phone    = preg_replace('/[^\d]/', '', $phone);
        $response = $this->get('recipients/phone/+'.$phone);

        if (null === $response) {
            return null;
        }

        return Recipient::fromJson($response->json(), $this);
    }

    public function createTask(array $data)
    {
        $response = $this->post('tasks', ['json' => $data]);

        $task = Task::fromJson($response->json(), $this);

        return [$task, $response];
    }

    /**
     * @param int|\DateTime $from The starting time of the range. Tasks created or completed at or after this time will be included.
     *                                 Millisecond precision int or DateTime
     * @param int|\DateTime $to Optional. If missing, defaults to the current time. The ending time of the range.
     *                                 Tasks created or completed before this time will be included.
     *                                 Millisecond precision int or DateTime
     * @param string $lastId Optional. Used to walk the paginated response, if there is one. Tasks created after this ID
     *                       will be returned, up to the per-query limit of 64.
     * @return Task[]
     */
    public function getTasks($from, $to = null, &$lastId = null): array
    {
        if ($from instanceof \DateTime) {
            $from = $from->getTimestamp() * 1000;
        }
        if ($to instanceof \DateTime) {
            $to = $to->getTimestamp() * 1000;
        }
        $query = array_filter([
            'from'   => $from,
            'to'     => $to,
            'lastId' => $lastId,
        ]);
        $response = $this->get('tasks/all', compact('query'));

        $tasks  = [];
        $json   = $response->json();
        $lastId = isset($json->lastId) ? $json->lastId : false;
        foreach ($json->tasks as $taskData) {
            $tasks[] = Task::fromJson($taskData, $this);
        }

        return $tasks;
    }

    /**
     * @param string $id
     * @return Task
     */
    public function getTask($id): Task
    {
        $response = $this->get('tasks/'.$id);
        return Task::fromJson($response->json(), $this);
    }

    /**
     * @param string $shortId
     * @return Task
     */
    public function getTaskByShortId($shortId): Task
    {
        $response = $this->get('tasks/shortId/'.$shortId);
        return Task::fromJson($response->json(), $this);
    }

    /**
     * Replace all organization's tasks.
     *
     * @param array $taskIds
     * @param string $organizationId
     */
    public function setOrganizationTasks(array $taskIds, $organizationId)
    {
        $this->setContainerTasks('organizations', $organizationId, $taskIds);
    }

    /**
     * @param array $taskIds
     * @param string $organizationId
     */
    public function addTasksToOrganization(array $taskIds, $organizationId)
    {
        $this->setContainerTasks('organizations', $organizationId, $taskIds, -1);
    }

    /**
     * Replace all team's tasks.
     *
     * @param array $taskIds
     * @param string $teamId
     */
    public function setTeamTasks(array $taskIds, $teamId)
    {
        $this->setContainerTasks('teams', $teamId, $taskIds);
    }

    /**
     * @param array $taskIds
     * @param string $teamId
     */
    public function addTasksToTeam(array $taskIds, $teamId)
    {
        $this->setContainerTasks('teams', $teamId, $taskIds, -1);
    }

    /**
     * Replace all worker's tasks.
     *
     * @param array $taskIds
     * @param string $workerId
     */
    public function setWorkerTasks(array $taskIds, $workerId)
    {
        $this->setContainerTasks('workers', $workerId, $taskIds);
    }

    /**
     * @param array $taskIds
     * @param string $workerId
     */
    public function addTasksToWorker(array $taskIds, $workerId)
    {
        $this->setContainerTasks('workers', $workerId, $taskIds, -1);
    }

    /**
     * @param string $url
     * @param int $triggerId
     * @return Webhook
     */
    public function createWebhook($url, $triggerId): Webhook
    {
        $response = $this->post('webhooks', [
            'json' => [
                'url'     => $url,
                'trigger' => $triggerId,
            ]
        ]);

        return Webhook::fromJson($response->json(), $this);
    }

    /**
     * @return Webhook[]
     */
    public function getWebhooks(): array
    {
        $response = $this->get('webhooks');

        $webhooks = [];
        foreach ($response->json() as $webhookData) {
            $webhooks[] = Webhook::fromJson($webhookData, $this);
        }

        return $webhooks;
    }

    /**
     * @param string $containerEndpoint "organizations", "workers" or "teams"
     * @param string $targetId ID of organization, worker or team.
     * @param array $taskIds Array of task IDs.
     * @param int $position Insert tasks at a given index. To append to the end, use -1, to prepend, use 0.
     * @throws \InvalidArgumentException
     */
    private function setContainerTasks($containerEndpoint, $targetId, array $taskIds, $position = null)
    {
        if (null !== $position) {
            if (!is_numeric($position)) {
                throw new \InvalidArgumentException('Position argument should be numeric, -1 for appending, 0 to prepend');
            }

            array_unshift($taskIds, $position);
        }

        $this->put('containers/'.$containerEndpoint.'/'.$targetId, [
            'json' => [
                'tasks' => $taskIds
            ]
        ]);
    }
}
