<?php

use dbarbar\CampfireHandler;
use Monolog\Logger;

class CampfireHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $campfire;

    public function setUp()
    {
        // I'm confused why this won't take constructor arguments.
        // The real class wants a config array.
        $this->campfire = $this->getMock('rcrowe\campfire', array('send'));
    }

    public function testCampfireHandlerIsMonologHandler()
    {
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', new CampfireHandler($this->campfire));
    }

    public function testCampfireHandlerConstruct()
    {
        $handler = new CampfireHandler($this->campfire, Logger::WARNING, false);

        $this->assertEquals(Logger::WARNING, $handler->getLevel());
        $this->assertFalse($handler->getBubble());
        $this->assertSame($this->campfire, $handler->getCampfire());
    }

    /**
     * Creates a record and sends it to the handler
     * to test that the campfire library's send()
     * is being called and passed a line formatted string.
     */
    public function testCampfireHandlerHandle()
    {
        $channel = 'testing';
        $message = 'Hello there';
        $level = Logger::ALERT;
        $levelName = Logger::getLevelName($level);
        $time = 1234;
        $record = array(
            'message' => $message,
            'context' => array(),
            'level' => $level,
            'level_name' => $levelName,
            'channel' => $channel,
            'datetime' => $time,
            'extra' => array(),
        );

        $formatted_message = "[$time] $channel.$levelName: $message [] []\n";
        $this->campfire->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($formatted_message));
        $handler = new CampfireHandler($this->campfire);
        $handler->handle($record);
    }
}
