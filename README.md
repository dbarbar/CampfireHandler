# Monolog Handler for posting to Campfire

This is my first attempt at making a Campfire handler for Monolog.

## Build Status

[![Build Status](https://travis-ci.org/dbarbar/CampfireHandler.png?branch=master)](undefined)

## Installation

Add `"dbarbar/campfire-monolog-handler": "dev-master"` to the require section of your composer.json.

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

/**
 * Parameters to CampfireHandler()
 * Instance of the Campfire object.
 * Minimum level to log. Defaults to Logger::DEBUG.
 * Bubble boolean. Defaults to true.
 * (Don't bubble up the message if this handler handles it.)
 */
$handler = new CampfireHandler(new Campfire($campfireConfig));

$log->pushHandler($handler);

// add records to the log
$log->warning('Foo');
$log->error('Bar');

````
