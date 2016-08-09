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
}
