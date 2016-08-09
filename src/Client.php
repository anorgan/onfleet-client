<?php

namespace OnFleet;

use GuzzleHttp\Client as Guzzle;

class Client extends Guzzle
{
    const BASE_URL = 'https://onfleet.com/api/{version}/';
    const VERSION  = 'v2';

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
        return Organization::fromJson($response->json(['object' => true]));
    }

    /**
     * @param array $data {
     *     @var string  $name       The administrator’s complete name.
     *     @var string  $email      The administrator’s email address.
     *     @var string  $phone      Optional. The administrator’s phone number.
     *     @var boolean $isReadOnly Optional. Whether this administrator can perform write operations.
     * }
     * @return mixed
     */
    public function createAdministrator(array $data)
    {
        $response = $this->post('admins', ['json' => $data]);
        return Administrator::fromJson($response->json(['object' => true]));
    }
}
