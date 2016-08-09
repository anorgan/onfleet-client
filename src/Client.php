<?php

namespace OnFleet;

use GuzzleHttp\Client as Guzzle;

class Client extends Guzzle
{
    const BASE_URL = 'https://onfleet.com/api/{version}/';
    const VERSION  = 'v2';

    /**
     * Client constructor.
     * @param array $username
     * @param array $config
     */
    public function __construct($username, array $config = [])
    {
        $version = isset($config['version']) ? $config['version'] : static::VERSION;

        if (!isset($config['base_url'])) {
            $config['base_url'] = [
                static::BASE_URL,
                ['version' => $version]
            ];
        }
        $config['defaults']['auth'] = [$username, null];

        parent::__construct($config);
    }

    /**
     * @return Organization
     */
    public function getMyOrganization()
    {
        $response = $this->get('organization');
        return Organization::fromJson($response->json(['object' => true]), $this);
    }

    /**
     * @param array $data {
     *     @var string  $name       The administrator’s complete name.
     *     @var string  $email      The administrator’s email address.
     *     @var string  $phone      Optional. The administrator’s phone number.
     *     @var boolean $isReadOnly Optional. Whether this administrator can perform write operations.
     * }
     * @return Administrator
     */
    public function createAdministrator(array $data)
    {
        $response = $this->post('admins', ['json' => $data]);
        return Administrator::fromJson($response->json(['object' => true]), $this);
    }

    /**
     * @return Administrator[]
     */
    public function getAdministrators()
    {
        $response = $this->get('admins');

        $administrators = [];
        foreach ($response->json(['object' => true]) as $administratorData) {
            $administrators[] = Administrator::fromJson($administratorData, $this);
        }

        return $administrators;
    }

    /**
     * @param array $data {
     *     @var string  $name       The worker’s complete name.
     *     @var string  $phone      A valid phone number as per the worker’s organization’s country.
     *     @var string|array $teams One or more team IDs of which the worker is a member.
     *     @var object  $vehicle    Optional. The worker’s vehicle; providing no vehicle details is equivalent to the
     *                              worker being on foot.
     *       @var string $type          The vehicle’s type, must be one of CAR, MOTORCYCLE, BICYCLE or TRUCK.
     *       @var string $description   Optional. The vehicle’s make, model, year, or any other relevant identifying details.
     *       @var string $licensePlate  Optional. The vehicle’s license plate number.
     *       @var string $color         Optional. The vehicle's color.
     *     @var integer $capacity   Optional. The maximum number of units this worker can carry, for route optimization purposes.
     * }
     * @return Worker
     */
    public function createWorker(array $data)
    {
        $response = $this->post('workers', ['json' => $data]);
        return Worker::fromJson($response->json(['object' => true]), $this);
    }

    /**
     * @param string $filter Optional. A comma-separated list of fields to return, if all are not desired. For example, name, location.
     * @param string $teams  Optional. A comma-separated list of the team IDs that workers must be part of.
     * @param string $states Optional. A comma-separated list of worker states, where
     *                       0 is off-duty, 1 is idle (on-duty, no active task) and 2 is active (on-duty, active task).
     * @return Worker[]
     */
    public function getWorkers($filter = null, $teams = null, $states = null)
    {
        $response = $this->get('workers', [
            'query' => [
                'filter' => $filter,
                'teams'  => $teams,
                'states' => $states,
            ],
        ]);

        $workers = [];
        foreach ($response->json(['object' => true]) as $administratorData) {
            $workers[] = Worker::fromJson($administratorData, $this);
        }

        return $workers;
    }
}
