# Monolog Handler for posting to Campfire

This is my first attempt at making a Campfire handler for Monolog.

## Usage Example

````php

use Monolog\Logger;
use dbarbar\CampfireHandler;
use rcrowe\Campfire;

// create a log channel
$log = new Logger('My Channel');

$campfireConfig = array(
  'key' => 'campfire token',
  'room' => 'room id number',
  'subdomain' => 'sudbdomain/account name',
  );
$handler = new CampfireHandler(Logger::NOTICE, false, new Campfire($campfireConfig));

$log->pushHandler($handler);

// add records to the log
$log->addWarning('Foo');
$log->addError('Bar');

````
