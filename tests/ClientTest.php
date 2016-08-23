<?php

namespace Anorgan\OnFleet\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Anorgan\OnFleet\Administrator;
use Anorgan\OnFleet\Client;
use Anorgan\OnFleet\Destination;
use Anorgan\OnFleet\Hub;
use Anorgan\OnFleet\Organization;
use Anorgan\OnFleet\Recipient;
use Anorgan\OnFleet\Task;
use Anorgan\OnFleet\Team;
use Anorgan\OnFleet\Webhook;
use Anorgan\OnFleet\Worker;

/**
 * Class ClientTest
 * @package Anorgan\OnFleet\Tests
 * @covers Anorgan\OnFleet\Client
 */
class ClientTest extends ApiTestCase
{
    /**
     * @covers Anorgan\OnFleet\Client::getMyOrganization
     * @covers Anorgan\OnFleet\Organization
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
     * @covers Anorgan\OnFleet\Client::getOrganization
     * @covers Anorgan\OnFleet\Organization
     */
    public function testGettingDelegateeOrganizationByIdReturnsOrganization()
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
     * @covers Anorgan\OnFleet\Client::createAdministrator
     * @covers Anorgan\OnFleet\Administrator
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
            'name'  => 'Admin Dispatcher',
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
     * @covers Anorgan\OnFleet\Client::getAdministrators
     * @covers Anorgan\OnFleet\Administrator
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
     * @covers Anorgan\OnFleet\Client::createWorker
     * @covers Anorgan\OnFleet\Worker
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
            'name'  => 'Worker Workowsky',
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
     * @covers Anorgan\OnFleet\Client::getWorkers
     * @covers Anorgan\OnFleet\Worker
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
     * @covers Anorgan\OnFleet\Client::getWorker
     * @covers Anorgan\OnFleet\Worker
     */
    public function testGettingWorkerByIdReturnsWorker()
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
     * @covers Anorgan\OnFleet\Client::getHubs
     * @covers Anorgan\OnFleet\Hub
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
     * @covers Anorgan\OnFleet\Client::createTeam
     * @covers Anorgan\OnFleet\Team
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
            'name'    => 'Team',
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
     * @covers Anorgan\OnFleet\Client::getTeams
     * @covers Anorgan\OnFleet\Team
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
     * @covers Anorgan\OnFleet\Client::getTeam
     * @covers Anorgan\OnFleet\Team
     */
    public function testGettingTeamByIdReturnsTeam()
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
     * @covers Anorgan\OnFleet\Client::createDestination
     * @covers Anorgan\OnFleet\Destination
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
                'number'     => '23',
                'street'     => 'Howard Street',
                'apartment'  => '5th Floor',
                'city'       => 'San Francisco',
                'country'    => 'United States',
                'state'      => 'California',
                'postalCode' => '94105',
            ],
            'notes'    => 'Don\'t forget to check out the epic rooftop.',
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
     * @covers Anorgan\OnFleet\Client::getDestination
     * @covers Anorgan\OnFleet\Destination
     */
    public function testGettingDestinationByIdReturnsDestination()
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

    /**
     * @covers Anorgan\OnFleet\Client::createRecipient
     * @covers Anorgan\OnFleet\Recipient
     */
    public function testCreatingRecipientCreatesAndReturnsRecipient()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "VVLx5OdKvw0dRSjT2rGOc6Y*",
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "timeCreated": 1455156665000,
                "timeLastModified": 1455156665390,
                "name": "Boris Foster",
                "phone": "+16505551133",
                "notes": "Always orders our GSC special",
                "skipSMSNotifications": false,
                "metadata": []
            }
            ')));

        $data = [
            'name'  => 'Boris Foster',
            'phone' => '650-555-1133',
            'notes' => 'Always orders our GSC special'
        ];
        $recipient = $this->client->createRecipient($data);

        $this->assertRequestIsPost('recipients', $data);
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('VVLx5OdKvw0dRSjT2rGOc6Y*', $recipient->getId());
        $this->assertEquals('Always orders our GSC special', $recipient->getNotes());
        $this->assertEquals('+16505551133', $recipient->getPhone());
    }

    /**
     * @covers Anorgan\OnFleet\Client::getRecipient
     * @covers Anorgan\OnFleet\Recipient
     */
    public function testGettingRecipientByIdReturnsRecipient()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "VVLx5OdKvw0dRSjT2rGOc6Y*",
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "timeCreated": 1455156665000,
                "timeLastModified": 1455156665390,
                "name": "Boris Foster",
                "phone": "+16505551133",
                "notes": "Always orders our GSC special",
                "skipSMSNotifications": false,
                "metadata": []
            }
            ')));

        $recipient = $this->client->getRecipient('VVLx5OdKvw0dRSjT2rGOc6Y*');

        $this->assertRequestIsGet('recipients/VVLx5OdKvw0dRSjT2rGOc6Y*');
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('VVLx5OdKvw0dRSjT2rGOc6Y*', $recipient->getId());
        $this->assertFalse($recipient->isSMSNotificationSkipped());
    }

    /**
     * @covers Anorgan\OnFleet\Client::getRecipientByName
     * @covers Anorgan\OnFleet\Recipient
     */
    public function testGettingRecipientByNameReturnsRecipient()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "VVLx5OdKvw0dRSjT2rGOc6Y*",
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "timeCreated": 1455156665000,
                "timeLastModified": 1455156665390,
                "name": "Boris Foster",
                "phone": "+16505551133",
                "notes": "Always orders our GSC special",
                "skipSMSNotifications": false,
                "metadata": []
            }
            ')));

        $recipient = $this->client->getRecipientByName('Boris Foster');

        $this->assertRequestIsGet('recipients/name/boris%20foster');
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('VVLx5OdKvw0dRSjT2rGOc6Y*', $recipient->getId());
        $this->assertFalse($recipient->isSMSNotificationSkipped());
    }

    /**
     * @covers Anorgan\OnFleet\Client::getRecipientByPhone
     * @covers Anorgan\OnFleet\Recipient
     */
    public function testGettingRecipientByPhoneReturnsRecipient()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "VVLx5OdKvw0dRSjT2rGOc6Y*",
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "timeCreated": 1455156665000,
                "timeLastModified": 1455156665390,
                "name": "Boris Foster",
                "phone": "+16505551133",
                "notes": "Always orders our GSC special",
                "skipSMSNotifications": false,
                "metadata": []
            }
            ')));

        $recipient = $this->client->getRecipientByPhone('(650)-555-1133');

        $this->assertRequestIsGet('recipients/phone/6505551133');
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('VVLx5OdKvw0dRSjT2rGOc6Y*', $recipient->getId());
        $this->assertFalse($recipient->isSMSNotificationSkipped());
    }

    /**
     * @covers Anorgan\OnFleet\Client::createTask
     * @covers Anorgan\OnFleet\Task
     */
    public function testCreatingATaskReturnsTask()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "kc8SS1tzuZ~jqjlebKGrUmpe",
                "timeCreated": 1455156667000,
                "timeLastModified": 1455156667234,
                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                "shortId": "8f983639",
                "trackingURL": "https://onf.lt/8f98363993",
                "worker": "1LjhGUWdxFbvdsTAAXs0TFos",
                "merchant": "yAM*fDkztrT3gUcz9mNDgNOL",
                "executor": "yAM*fDkztrT3gUcz9mNDgNOL",
                "creator": "EJmsbJgHiRLPjNVE7GEIPs7*",
                "dependencies": [],
                "state": 0,
                "completeAfter": 1455151071727,
                "completeBefore": null,
                "pickupTask": false,
                "notes": "Order 332: 24oz Stumptown Finca El Puente, 10 x Aji de Gallina Empanadas, 13-inch Lelenitas Tres Leches",
                "completionDetails": {
                    "events": [],
                    "time": null
                },
                "feedback": [],
                "metadata": [],
                "overrides": {
                    "recipientSkipSMSNotifications": null,
                    "recipientNotes": null,
                    "recipientName": null
                },
                "container": {
                    "type": "WORKER",
                    "worker": "1LjhGUWdxFbvdsTAAXs0TFos"
                },
                "recipients": [
                    {
                        "id": "G7rcM2nqblmh8vj2do1FpaOQ",
                        "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                        "timeCreated": 1455156667000,
                        "timeLastModified": 1455156667229,
                        "name": "Blas Silkovich",
                        "phone": "+16505554481",
                        "notes": "Knows Neiman, VIP status.",
                        "skipSMSNotifications": false,
                        "metadata": []
                    }
                ],
                "destination": {
                    "id": "zrVXZi5aDzOZlAghZaLfGAfA",
                    "timeCreated": 1455156667000,
                    "timeLastModified": 1455156667220,
                    "location": [
                        -122.4438337,
                        37.7940329
                    ],
                    "address": {
                        "apartment": "",
                        "state": "California",
                        "postalCode": "94123",
                        "country": "United States",
                        "city": "San Francisco",
                        "street": "Vallejo Street",
                        "number": "2829"
                    },
                    "notes": "Small green door by garage door has pin pad, enter *4821*",
                    "metadata": []
                },
                "didAutoAssign": true
            }
            ')));

        $data = [
            'destination' => 'zrVXZi5aDzOZlAghZaLfGAfA',
            'recipients'  => [
                'G7rcM2nqblmh8vj2do1FpaOQ'
            ],
            'merchant'    => 'cBrUjKvQQgdRp~s1qvQNLpK*',
            'executor'    => 'cBrUjKvQQgdRp~s1qvQNLpK*',
            'autoAssign'  => [
                'mode' => 'load',
            ],
        ];
        $task = $this->client->createTask($data);

        $this->assertRequestIsPost('tasks', $data);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertTrue($task->isAutoAssigned());
        $this->assertEquals('1LjhGUWdxFbvdsTAAXs0TFos', $task->getWorker());
        $this->assertEquals(0, $task->getState());
    }

    /**
     * @covers Anorgan\OnFleet\Client::getTasks
     * @covers Anorgan\OnFleet\Task
     */
    public function testGettingTasksReturnsArrayOfTasks()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "lastId": "tPMO~h03sOIqFbnhqaOXgUsd",
                "tasks": [
                    {
                        "id": "11z1BbsQUZFHD1XAd~emDDeK",
                        "timeCreated": 1455072025000,
                        "timeLastModified": 1455072025278,
                        "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                        "shortId": "31aac0a5",
                        "trackingURL": "https://onf.lt/31aac0a5c",
                        "worker": "h*wSb*apKlDkUFnuLTtjPke7",
                        "merchant": "yAM*fDkztrT3gUcz9mNDgNOL",
                        "executor": "yAM*fDkztrT3gUcz9mNDgNOL",
                        "creator": "EJmsbJgHiRLPjNVE7GEIPs7*",
                        "dependencies": [],
                        "state": 1,
                        "completeAfter": null,
                        "completeBefore": null,
                        "pickupTask": false,
                        "notes": "",
                        "completionDetails": {
                            "events": [],
                            "time": null
                        },
                        "feedback": [],
                        "metadata": [],
                        "overrides": {},
                        "container": {
                            "type": "WORKER",
                            "worker": "h*wSb*apKlDkUFnuLTtjPke7"
                        },
                        "recipients": [
                            {
                                "id": "xX87G1gSkeLvGXlHn2tn0~iB",
                                "organization": "yAM*fDkztrT3gUcz9mNDgNOL",
                                "timeCreated": 1455072004000,
                                "timeLastModified": 1455072025272,
                                "name": "Blake Turing",
                                "phone": "+16505552811",
                                "notes": "",
                                "skipSMSNotifications": false,
                                "metadata": []
                            }
                        ],
                        "destination": {
                            "id": "pfT5L1JclTdhvRnP9GQzMFuL",
                            "timeCreated": 1455072025000,
                            "timeLastModified": 1455072025264,
                            "location": [
                                -122.41289010000003,
                                37.787933
                            ],
                            "address": {
                                "apartment": "",
                                "state": "California",
                                "postalCode": "94109",
                                "country": "United States",
                                "city": "San Francisco",
                                "street": "Post Street",
                                "number": "666"
                            },
                            "notes": "",
                            "metadata": []
                        }
                    }
                ]
            }
            ')));
        $from = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-08-20 16:20:00');
        $to   = clone $from;
        $to->add(new \DateInterval('PT10M'));
        $lastId = null;

        $tasks = $this->client->getTasks($from, $to, $lastId);

        $this->assertRequestIsGet('tasks/all?from=1471710000000&to=1471710600000');
        $this->assertCount(1, $tasks);
        $this->assertEquals('tPMO~h03sOIqFbnhqaOXgUsd', $lastId);
    }

    /**
     * @covers Anorgan\OnFleet\Client::getTask
     * @covers Anorgan\OnFleet\Task
     */
    public function testGettingTaskByIdReturnsTask()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "11z1BbsQUZFHD1XAd~emDDeK"
            }
            ')));

        $task = $this->client->getTask('11z1BbsQUZFHD1XAd~emDDeK');

        $this->assertRequestIsGet('tasks/11z1BbsQUZFHD1XAd~emDDeK');
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('11z1BbsQUZFHD1XAd~emDDeK', $task->getId());
    }

    /**
     * @covers Anorgan\OnFleet\Client::getTaskByShortId
     * @covers Anorgan\OnFleet\Task
     */
    public function testGettingTaskByShortIdReturnsTask()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "11z1BbsQUZFHD1XAd~emDDeK",
                "shortId": "31aac0a5"
            }
            ')));

        $task = $this->client->getTaskByShortId('31aac0a5');

        $this->assertRequestIsGet('tasks/shortId/31aac0a5');
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('11z1BbsQUZFHD1XAd~emDDeK', $task->getId());
        $this->assertEquals('31aac0a5', $task->getShortId());
    }

    /**
     * @covers Anorgan\OnFleet\Client::setOrganizationTasks
     */
    public function testSettingOrganizationTasks()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->setOrganizationTasks($taskIds, 'yAM*fDkztrT3gUcz9mNDgNOL');

        $this->assertRequestIsPut('containers/organizations/yAM*fDkztrT3gUcz9mNDgNOL', [
            'tasks' => $taskIds
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::setTeamTasks
     */
    public function testSettingTeamTasks()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->setTeamTasks($taskIds, 'E4s6bwGpOZp6pSU3Hz*2ngFA');

        $this->assertRequestIsPut('containers/teams/E4s6bwGpOZp6pSU3Hz*2ngFA', [
            'tasks' => $taskIds
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::setWorkerTasks
     */
    public function testSettingWorkerTasks()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->setWorkerTasks($taskIds, 'h*wSb*apKlDkUFnuLTtjPke7');

        $this->assertRequestIsPut('containers/workers/h*wSb*apKlDkUFnuLTtjPke7', [
            'tasks' => $taskIds
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::addTasksToOrganization
     */
    public function testAddingTasksToOrganization()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->addTasksToOrganization($taskIds, 'yAM*fDkztrT3gUcz9mNDgNOL');

        $this->assertRequestIsPut('containers/organizations/yAM*fDkztrT3gUcz9mNDgNOL', [
            'tasks' => [
                -1,
                '11z1BbsQUZFHD1XAd~emDDeK',
                'kc8SS1tzuZ~jqjlebKGrUmpe'
            ]
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::addTasksToTeam
     */
    public function testAddingTasksToTeam()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->addTasksToTeam($taskIds, 'E4s6bwGpOZp6pSU3Hz*2ngFA');

        $this->assertRequestIsPut('containers/teams/E4s6bwGpOZp6pSU3Hz*2ngFA', [
            'tasks' => [
                -1,
                '11z1BbsQUZFHD1XAd~emDDeK',
                'kc8SS1tzuZ~jqjlebKGrUmpe'
            ]
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::addTasksToWorker
     */
    public function testAddingTasksToWorker()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json']));

        $taskIds = [
            '11z1BbsQUZFHD1XAd~emDDeK',
            'kc8SS1tzuZ~jqjlebKGrUmpe'
        ];
        $this->client->addTasksToWorker($taskIds, 'h*wSb*apKlDkUFnuLTtjPke7');

        $this->assertRequestIsPut('containers/workers/h*wSb*apKlDkUFnuLTtjPke7', [
            'tasks' => [
                -1,
                '11z1BbsQUZFHD1XAd~emDDeK',
                'kc8SS1tzuZ~jqjlebKGrUmpe'
            ]
        ]);
    }

    /**
     * @covers Anorgan\OnFleet\Client::createWebhook
     * @covers Anorgan\OnFleet\Webhook
     */
    public function testCreatingWebhookReturnsWebhook()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            {
                "id": "9zqMxI79mRcHpXE111nILiPn",
                "count": 0,
                "url": "http://requestb.in/11sl22k1",
                "trigger": 6
            }
            ')));

        $webhook = $this->client->createWebhook('http://requestb.in/11sl22k1', Webhook::TRIGGER_TASK_CREATED);

        $this->assertRequestIsPost('webhooks', [
            'url'     => 'http://requestb.in/11sl22k1',
            'trigger' => 6
        ]);
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('http://requestb.in/11sl22k1', $webhook->getUrl());
        $this->assertEquals(6, $webhook->getTrigger());
    }
    /**
     * @covers Anorgan\OnFleet\Client::getWebhooks
     * @covers Anorgan\OnFleet\Webhook
     */
    public function testGettingWebhookReturnsArrayOfWebhooks()
    {
        $this->mockedResponses
            ->addResponse(new Response(200, ['Content-type' => 'application/json'], Stream::factory('
            [
                {
                    "id": "9zqMxI79mRcHpXE111nILiPn",
                    "count": 0,
                    "url": "http://requestb.in/11sl22k1",
                    "trigger": 6
                },
                {
                    "id": "9zqMxI79mRcHpXE111nILiPn",
                    "count": 9,
                    "url": "http://requestb.in/11sl22k1",
                    "trigger": 2
                }
            ]
            ')));

        $webhooks = $this->client->getWebhooks();

        $this->assertRequestIsGet('webhooks');
        $this->assertCount(2, $webhooks);
        $webhook = $webhooks[1];
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('http://requestb.in/11sl22k1', $webhook->getUrl());
        $this->assertEquals('9zqMxI79mRcHpXE111nILiPn', $webhook->getId());
        $this->assertEquals(9, $webhook->getCount());
        $this->assertEquals(2, $webhook->getTrigger());
    }
}
