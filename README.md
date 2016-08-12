# Onfleet API Client

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anorgan/onfleet-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anorgan/onfleet-client/?branch=master)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/anorgan/onfleet-client/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/anorgan/onfleet-client.svg?style=flat-square)](https://packagist.org/packages/anorgan/onfleet-client)

PHP API Client for [Onfleet](https://onfleet.com) service

## Instalation

Install via [Composer](http://getcomposer.org) by running `composer require anorgan/onfleet-client ^0.9`.

```json
{
    "require": {
       "anorgan/onfleet-api": "^0.9"
    }
}
```

## Usage

```php
<?php

$onfleet = new Onfleet\Client('onfleetUserName');

// Get your Organization
$organization = $onfleet->getMyOrganization();

// Create Administrator
$adminData = [
    'name'  => 'Marin Crnković',
    'email' => 'marin.crnkovic@gmail.com',
];
$administrator = $onfleet->createAdministrator($adminData);

// Get all administrators
$administrators = $onfleet->getAdministrators();

// Update administrator
$administrator->setName('Updated Name');
$updatedAdministrator = $administrator->update();

// Delete administrator
$updatedAdministrator->delete();

// Create Worker
$worker = $onfleet->createWorker([
    'name'  => 'Worker Workowsky',
    'phone' => '+1555123456',
    'teams' => 'team123'
]);

// Get all workers
$workers = $onfleet->getWorkers();

// Get single worker
$singleWorker = $onfleet->getWorker($worker->getId());

// Get hubs
$hubs = $onfleet->getHubs();

// Create team
$team = $onfleet->createTeam([
    'name' => 'Team',
    'workers' => [
        $worker->getId(),
        'worker2ID',
    ],
    'managers' => [
        $administrator->getId(),
    ],
]);

// Update team
$team->setHub($hubs[0]->getId());
$team->update();

// Get teams
$teams = $onfleet->getTeams();

// Delete Team
$team->delete();

// Create destination
$destination = $onfleet->createDestination([
    'address' => [
        'number'    => '543',
        'street'    => 'Howard St',
        'apartment' => '5th Floor',
        'city'      => 'San Francisco',
        'state'     => 'CA',
        'country'   => 'USA'
    ],
    'notes' => "Don't forget to check out the epic rooftop."
]);

// Get Destination
$destination = $onfleet->getDestination($destination->getId());

// Create recipient
$recipient = $onfleet->createRecipient([
   'name'   => 'Boris Foster',
   'phone'  => '650-555-1133',
   'notes'  => 'Always orders our GSC special'
]);

// Get single recipient
$recipient = $onfleet->getRecipient($recipient->getId());

// Update recipient
$recipient->setName('Updated name');
$recipient->update();

// Find by name
$recipient = $onfleet->getRecipientByName($recipient->getName());

// Find by phone
$recipient = $onfleet->getRecipientByPhone($recipient->getPhone());

// Create task
$taskArray = Task::createAutoAssignedArray($destination, $recipient);
$task      = $onfleet->createTask($taskArray);

// Get tasks
$tasks = $onfleet->getTasks(time() - 20);

// Get single task
$task = $onfleet->getTask($tasks[0]->getId());

// Get single task by short ID
$task = $onfleet->getTaskByShortId($tasks[0]->getShortId());

// Update task
$task->setNotes('Updated note');
$task->update();

// Complete task
$task->complete(true, 'Successful completion note');

// Delete task
$task->delete();

// Create webhook
$webhook = $onfleet->createWebhook('http://example.com/webhook/onfleet/taskCreated', \Onfleet\Webhook::TRIGGER_TASK_CREATED);

// Delete webhook
$webhook->delete();
```

Setting and retrieving metadata:

```php
<?php

// Entity which supports metadata
$entity->setMetadata([
    'string'       => 'String',
    'number'       => 123,
    'number_float' => 12.3,
    'boolean'      => true,
    'object'       => [
        'propery' => 'value'
    ],
    'array'        => [
        'alpha',
        'beta',
        'gamma'
    ]
]);

// Get structured metadata as API returns it
$metadata = $entity->getMetadata();

// Get normalized metadata, same as you would send to "setMetadata"
$normalizedMetadata = $entity->getNormalizedMetadata();
```

## Requirements

PHP 7.0 or above

## Licence

Onfleet PHP API client is licensed under the MIT License - see the LICENSE file for details

