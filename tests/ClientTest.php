<?php

namespace OnFleet\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use OnFleet\Administrator;
use OnFleet\Client;
use OnFleet\Destination;
use OnFleet\Hub;
use OnFleet\Organization;
use OnFleet\Team;
use OnFleet\Worker;

/**
 * Class ClientTest
 * @package OnFleet\Tests
 * @covers OnFleet\Client
 */
class ClientTest extends ApiTestCase
{
    /**
     * @covers OnFleet\Client::getMyOrganization
     * @covers OnFleet\Organization
     */
    public function testGettingMyOrganizationReturnsOrganization()
    {
        // Arrange
        $this->mockedResponses->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
        {
            "id": "yAM*fDkztrT3gUcz9mNDgNOL",
            "timeCreated": 1454634415000,
            "timeLastModified": 1455048510514,
            "name": "Onfleet Fine Eateries",
            "email": "fe@onfleet.com",
            "image": "5cc28fc91d7bc5846c6ce9c1",
            "timezone": "America/Los_Angeles",
            "country": "US",
            "delegatees": [
                "cBrUjKvQQgdRp~s1qvQNLpK*"
            ]
        }')));

        // Act
        $organization = $this->client->getMyOrganization();

        // Assert
        $this->assertRequestIsGet('organization');

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertEquals('yAM*fDkztrT3gUcz9mNDgNOL', $organization->getId());
        $this->assertEquals(\DateTime::createFromFormat('U', 1454634415), $organization->getTimeCreated());
        $this->assertEquals(\DateTime::createFromFormat('U', 1455048510), $organization->getTimeLastModified());

        $expectedDelegatees = [
            'cBrUjKvQQgdRp~s1qvQNLpK*'
        ];
        $this->assertEquals($expectedDelegatees, $organization->getDelegatees());
    }

    /**
     * @covers OnFleet\Client::getOrganization
     * @covers OnFleet\Organization
     */
    public function testGettingDelegateeOrganizationReturnsOrganization()
    {
        // Arrange
        $this->mockedResponses->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
        {
            "id": "cBrUjKvQQgdRp~s1qvQNLpK*",
            "name": "Onfleet Engineering",
            "email": "dev@onfleet.com",
            "timezone": "America/Los_Angeles",
            "country": "US"
        }')));

        // Act
        $organization = $this->client->getOrganization('cBrUjKvQQgdRp~s1qvQNLpK*');

        // Assert
        $this->assertRequestIsGet('organizations/cBrUjKvQQgdRp~s1qvQNLpK*');

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertEquals('cBrUjKvQQgdRp~s1qvQNLpK*', $organization->getId());
        $this->assertEquals('dev@onfleet.com', $organization->getEmail());
        $this->assertEquals('America/Los_Angeles', $organization->getTimezone());
        $this->assertEquals('US', $organization->getCountry());
    }

    /**
     * @covers OnFleet\Client::createAdministrator
     * @covers OnFleet\Administrator
     */
    public function testCreatingAdministratorCreatesAndReturnsAdministrator()
    {
        // Arrange
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('{
            "id": "8AxaiKwMd~np7I*YP2NfukBE",
            "timeCreated": 1455156651000,
            "timeLastModified": 1455156651779,
            "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
            "email": "dispatcher@example.com",
            "type": "standard",
            "name": "Admin Dispatcher",
            "isActive": false,
            "metadata": []
        }')));

        $adminData = [
            'name' => 'Admin Dispatcher',
            'email' => 'dispatcher@example.com',
        ];

        // Act
        $administrator = $this->client->createAdministrator($adminData);

        // Assert
        $this->assertRequestIsPost('admins', $adminData);

        $this->assertInstanceOf(Administrator::class, $administrator);
        $this->assertEquals('yAM*fDkztrT3gUcz9mNDgNOL', $administrator->getOrganization());
        $this->assertEquals('dispatcher@example.com', $administrator->getEmail());
        $this->assertEquals('Admin Dispatcher', $administrator->getName());
        $this->assertEquals('standard', $administrator->getType());
        $this->assertFalse($administrator->isActive());
    }

    /**
     * @covers OnFleet\Client::getAdministrators
     * @covers OnFleet\Administrator
     */
    public function testGettingAdministratorsReturnsArrayOfAdministrators()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            [
                {
                    "id": "8AxaiKwMd~np7I*YP2NfukBE",
                    "timeCreated": 1455156651000,
                    "timeLastModified": 1455156651779,
                    "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                    "email": "super.admin@example.com",
                    "type": "super",
                    "name": "Super Admin",
                    "isActive": true,
                    "metadata": []
                },
                {
                    "id": "8AxaiKwMd~np7I*YP2NfukBE",
                    "timeCreated": 1455156651000,
                    "timeLastModified": 1455156651779,
                    "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                    "email": "dispatcher.admin@example.com",
                    "type": "standard",
                    "name": "Dispatcher Admin",
                    "isActive": false,
                    "metadata": []
                }
            ]
            ')));

        $administrators = $this->client->getAdministrators();

        $this->assertRequestIsGet('admins');

        $this->assertCount(2, $administrators);
        $administrator = $administrators[0];
        $this->assertEquals('8AxaiKwMd~np7I*YP2NfukBE', $administrator->getId());
        $this->assertEquals('super', $administrator->getType());
        $this->assertTrue($administrator->isActive());
    }

    /**
     * @covers OnFleet\Client::createWorker
     * @covers OnFleet\Worker
     */
    public function testCreatingWorkerCreatesAndReturnsWorker()
    {
        // Arrange
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "sFtvhYK2l26zS0imptJJdC2q",
                "timeCreated": 1455156653000,
                "timeLastModified": 1455156653214,
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "name": "Worker Workowsky",
                "phone": "+16173428853",
                "activeTask": null,
                "tasks": [],
                "onDuty": false,
                "timeLastSeen": null,
                "delayTime": null,
                "teams": [
                    "nz1nG1Hpx9EHjQCJsT2VAs~o"
                ],
                "metadata": [],
                "vehicle": {
                    "id": "tN1HjcvygQWvz5FRR1JAxwL8",
                    "type": "CAR",
                    "description": "Tesla Model 3",
                    "licensePlate": "FKNS9A",
                    "color": "purple"
                }
            }
            ')));

        $data = [
            'name' => 'Worker Workowsky',
            'phone' => '+16173428853',
            'teams' => [
                'nz1nG1Hpx9EHjQCJsT2VAs~o'
            ]
        ];

        // Act
        $worker = $this->client->createWorker($data);

        // Assert
        $this->assertRequestIsPost('workers', $data);

        $this->assertInstanceOf(Worker::class, $worker);
        $this->assertEquals('sFtvhYK2l26zS0imptJJdC2q', $worker->getId());
        $this->assertEquals('Worker Workowsky', $worker->getName());
        $this->assertEquals('+16173428853', $worker->getPhone());
        $this->assertEquals([
            'nz1nG1Hpx9EHjQCJsT2VAs~o'
        ], $worker->getTeams());
        $this->assertEquals('Tesla Model 3', $worker->getVehicle()['description']);
        $this->assertFalse($worker->isOnDuty());
    }

    /**
     * @covers OnFleet\Client::getWorkers
     * @covers OnFleet\Worker
     */
    public function testGettingWorkersReturnsArrayOfWorkers()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            [
                {
                    "id": "h*wSb*apKlDkUFnuLTtjPke7",
                    "timeCreated": 1455049674000,
                    "timeLastModified": 1455156646529,
                    "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                    "name": "Andoni",
                    "phone": "+14155558442",
                    "activeTask": null,
                    "tasks": [
                        "11z1BbsQUZFHD1XAd~emDDeK"
                    ],
                    "onDuty": true,
                    "timeLastSeen": 1455156644323,
                    "delayTime": null,
                    "teams": [
                        "R4P7jhuzaIZ4cHHZE1ghmTtB"
                    ],
                    "metadata": [
                        {
                            "name": "nickname",
                            "type": "string",
                            "value": "Puffy",
                            "visibility": [
                                "api"
                            ]
                        },
                        {
                            "name": "otherDetails",
                            "type": "object",
                            "value": {
                                "availability": {
                                    "mon": "10:00",
                                    "sat": "16:20",
                                    "wed": "13:30"
                                },
                                "premiumInsurance": false,
                                "trunkSize": 9.5
                            },
                            "visibility": [
                                "api"
                            ]
                        }
                    ],
                    "location": [
                        -122.4015496466794,
                        37.77629837661284
                    ],
                    "vehicle": null
                },
                {
                    "id": "1LjhGUWdxFbvdsTAAXs0TFos",
                    "timeCreated": 1455049755000,
                    "timeLastModified": 1455072352267,
                    "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                    "name": "Yevgeny",
                    "phone": "+14155552299",
                    "activeTask": null,
                    "tasks": [
                        "*0tnJcly~vSI~9uHz*ICHXTw",
                        "PauBfRH8gQCjtMLaPe97G8Jf"
                    ],
                    "onDuty": true,
                    "timeLastSeen": 1455156649007,
                    "delayTime": null,
                    "teams": [
                        "9dyuPqHt6kDK5JKHFhE0xihh",
                        "yKpCnWprM1Rvp3NGGlVa5TMa",
                        "fwflFNVvrK~4t0m5hKFIxBUl"
                    ],
                    "metadata": [],
                    "location": [
                        -122.4016366,
                        37.7764098
                    ],
                    "vehicle": {
                        "id": "ArBoHNxS4B76AiBKoIawY9OS",
                        "type": "CAR",
                        "description": "Lada Niva",
                        "licensePlate": "23KJ129",
                        "color": "Red"
                    }
                }
            ]
            ')));

        $workers = $this->client->getWorkers();

        $this->assertRequestIsGet('workers');

        $this->assertCount(2, $workers);
        $worker = $workers[0];
        $this->assertEquals('h*wSb*apKlDkUFnuLTtjPke7', $worker->getId());
        $this->assertTrue($worker->isOnDuty());
        $this->assertFalse($worker->getNormalizedMetadata()['otherDetails']['premiumInsurance']);
    }

    /**
     * @covers OnFleet\Client::getWorker
     * @covers OnFleet\Worker
     */
    public function testGettingWorkerReturnsWorker()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "1LjhGUWdxFbvdsTAAXs0TFos",
                "timeCreated": 1455049755000,
                "timeLastModified": 1455072352267,
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "name": "Yevgeny",
                "phone": "+14155552299",
                "activeTask": null,
                "tasks": [
                    "*0tnJcly~vSI~9uHz*ICHXTw",
                    "PauBfRH8gQCjtMLaPe97G8Jf"
                ],
                "onDuty": true,
                "timeLastSeen": 1455156649007,
                "delayTime": null,
                "teams": [
                    "9dyuPqHt6kDK5JKHFhE0xihh",
                    "yKpCnWprM1Rvp3NGGlVa5TMa",
                    "fwflFNVvrK~4t0m5hKFIxBUl"
                ],
                "metadata": [],
                "location": [
                    -122.4016366,
                    37.7764098
                ],
                "vehicle": {
                    "id": "ArBoHNxS4B76AiBKoIawY9OS",
                    "type": "CAR",
                    "description": "Lada Niva",
                    "licensePlate": "23KJ129",
                    "color": "Red"
                }
            }
            ')));

        $worker = $this->client->getWorker('1LjhGUWdxFbvdsTAAXs0TFos');

        $this->assertRequestIsGet('workers/1LjhGUWdxFbvdsTAAXs0TFos?analytics=false');
        $this->assertInstanceOf(Worker::class, $worker);
    }

    /**
     * @covers OnFleet\Client::getHubs
     * @covers OnFleet\Hub
     */
    public function testGettingHubsReturnsArrayOfHubs()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            [
                {
                    "id": "E4s6bwGpOZp6pSU3Hz*2ngFA",
                    "name": "SF North",
                    "location": [
                        -122.44002499999999,
                        37.801826
                    ],
                    "address": {
                        "number": "3415",
                        "street": "Pierce Street",
                        "city": "San Francisco",
                        "state": "California",
                        "country": "United States",
                        "postalCode": "94123"
                    }
                },
                {
                    "id": "tKxSfU7psqDQEBVn5e2VQ~*O",
                    "name": "SF South",
                    "location": [
                        -122.44337999999999,
                        37.70883
                    ],
                    "address": {
                        "number": "335",
                        "street": "Hanover Street",
                        "city": "San Francisco",
                        "state": "California",
                        "country": "United States",
                        "postalCode": "94112"
                    }
                } 
            ]
            ')));

        $hubs = $this->client->getHubs();

        $this->assertRequestIsGet('hubs');

        $this->assertCount(2, $hubs);
        $hub = $hubs[0];
        $this->assertInstanceOf(Hub::class, $hub);
        $this->assertEquals('94123', $hub->getAddress()['postalCode']);
    }

    /**
     * @covers OnFleet\Client::createTeam
     * @covers OnFleet\Team
     */
    public function testCreatingTeamCreatesAndReturnsTeam()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "teamiKwMd~np7I*YP2NfukBE",
                "name": "Team",
                "workers": [
                    "sFtvhYK2l26zS0imptJJdC2q",
                    "h*wSb*apKlDkUFnuLTtjPke7"
                ],
                "managers": [
                    "8AxaiKwMd~np7I*YP2NfukBE"
                ],
                "hub": "E4s6bwGpOZp6pSU3Hz*2ngFA",
                "timeCreated": 1455156651000,
                "timeLastModified": 1455156651779
            }
            ')));

        $data = [
            'name' => 'Team',
            'workers' => [
                'sFtvhYK2l26zS0imptJJdC2q',
                'h*wSb*apKlDkUFnuLTtjPke7',
            ],
            'managers' => [
                '8AxaiKwMd~np7I*YP2NfukBE',
            ],
            'hub' => 'E4s6bwGpOZp6pSU3Hz*2ngFA',
        ];
        $team = $this->client->createTeam($data);

        $this->assertRequestIsPost('teams', $data);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Team', $team->getName());
        $this->assertEquals('E4s6bwGpOZp6pSU3Hz*2ngFA', $team->getHub());
    }

    /**
     * @covers OnFleet\Client::getTeams
     * @covers OnFleet\Team
     */
    public function testGettingTeamsReturnsArrayOfTeams()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            [
                {
                    "id": "teamiKwMd~np7I*YP2NfukBE",
                    "name": "Team",
                    "workers": [
                        "sFtvhYK2l26zS0imptJJdC2q",
                        "h*wSb*apKlDkUFnuLTtjPke7"
                    ],
                    "managers": [
                        "8AxaiKwMd~np7I*YP2NfukBE"
                    ],
                    "hub": "E4s6bwGpOZp6pSU3Hz*2ngFA",
                    "timeCreated": 1455156651000,
                    "timeLastModified": 1455156651779
                }
            ]
            ')));

        $teams = $this->client->getTeams();

        $this->assertRequestIsGet('teams');

        $this->assertCount(1, $teams);
        $team = $teams[0];
        $this->assertEquals('teamiKwMd~np7I*YP2NfukBE', $team->getId());
    }

    /**
     * @covers OnFleet\Client::getTeam
     * @covers OnFleet\Team
     */
    public function testGettingTeamReturnsTeam()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "teamiKwMd~np7I*YP2NfukBE",
                "name": "Team",
                "workers": [
                    "sFtvhYK2l26zS0imptJJdC2q",
                    "h*wSb*apKlDkUFnuLTtjPke7"
                ],
                "managers": [
                    "8AxaiKwMd~np7I*YP2NfukBE"
                ],
                "hub": "E4s6bwGpOZp6pSU3Hz*2ngFA",
                "timeCreated": 1455156651000,
                "timeLastModified": 1455156651779
            }
            ')));

        $team = $this->client->getTeam('teamiKwMd~np7I*YP2NfukBE');

        $this->assertRequestIsGet('teams/teamiKwMd~np7I*YP2NfukBE');
        $this->assertInstanceOf(Team::class, $team);
    }

    /**
     * @covers OnFleet\Client::createDestination
     * @covers OnFleet\Destination
     */
    public function testCreatingDestinationReturnsDestination()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "JLn6ZoYGZWn2wB2HaR9glsqB",
                "timeCreated": 1455156663000,
                "timeLastModified": 1455156663896,
                "location": [
                    -122.3965731,
                    37.7875728
                ],
                "address": {
                    "apartment": "5th Floor",
                    "state": "California",
                    "postalCode": "94105",
                    "country": "United States",
                    "city": "San Francisco",
                    "street": "Howard Street",
                    "number": "543"
                },
                "notes": "Don\'t forget to check out the epic rooftop.",
                "metadata": []
            }
            ')));

        $data = [
            'address' => [
                'number' => '23',
                'street' => 'Howard Street',
                'apartment' => '5th Floor',
                'city' => 'San Francisco',
                'country' => 'United States',
                'state' => 'California',
                'postalCode' => '94105',
            ],
            'notes' => 'Don\'t forget to check out the epic rooftop.',
            'location' => [
                -122.3965731,
                37.7875728,
            ]
        ];
        $destination = $this->client->createDestination($data);

        $this->assertRequestIsPost('destinations', $data);
        $this->assertInstanceOf(Destination::class, $destination);
        $this->assertEquals('JLn6ZoYGZWn2wB2HaR9glsqB', $destination->getId());
        $this->assertEquals('Don\'t forget to check out the epic rooftop.', $destination->getNotes());
    }

    /**
     * @covers OnFleet\Client::getDestination
     * @covers OnFleet\Destination
     */
    public function testGettingDestinationReturnsDestination()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "JLn6ZoYGZWn2wB2HaR9glsqB",
                "timeCreated": 1455156663000,
                "timeLastModified": 1455156663896,
                "location": [
                    -122.3965731,
                    37.7875728
                ],
                "address": {
                    "apartment": "5th Floor",
                    "state": "California",
                    "postalCode": "94105",
                    "country": "United States",
                    "city": "San Francisco",
                    "street": "Howard Street",
                    "number": "543"
                },
                "notes": "Don\'t forget to check out the epic rooftop.",
                "metadata": []
            }
            ')));

        $destination = $this->client->getDestination('JLn6ZoYGZWn2wB2HaR9glsqB');

        $this->assertRequestIsGet('destinations/JLn6ZoYGZWn2wB2HaR9glsqB');
        $this->assertInstanceOf(Destination::class, $destination);
        $this->assertEquals('JLn6ZoYGZWn2wB2HaR9glsqB', $destination->getId());
        $this->assertEquals([
            -122.3965731,
            37.7875728
        ], $destination->getLocation());
    }
}
