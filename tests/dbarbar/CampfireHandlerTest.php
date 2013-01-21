<?php

use dbarbar\CampfireHandler;
use Monolog\Logger;

class CampfireHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $campfire;
    protected $formatter;
    protected $record;
    protected $formatted_string;

    public function setUp()
    {
        // I'm confused why this won't take constructor arguments.
        // The real class wants a config array.
        $this->campfire = $this->getMock('rcrowe\campfire', array('send'));

        $this->formatter = $this->getMock('Monolog\Formatter\FormatterInterface', array('format', 'formatBatch'));

        $this->record = array(
            'message' => 'Hello there',
            'context' => array(),
            'level' => Logger::ALERT,
            'level_name' => Logger::getLevelName(Logger::ALERT),
            'channel' => 'testing',
            'datetime' => 1234,
            'extra' => array(),
        );

        $this->formatted_string = "[1234] test.ALERT: Hello there [] []\n";
    }

    public function tearDown()
    {
        $this->campfire = null;
        $this->formatter = null;
        $this->record = null;
        $this->formatted_string = null;
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
     * and the formatter's format()
     * are being called and passed a what they expect.
     */
    public function testCampfireHandlerHandle()
    {
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->will($this->returnValue($this->formatted_string));
        $this->campfire->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($this->formatted_string));
        $handler = new CampfireHandler($this->campfire);
        $handler->setFormatter($this->formatter);
        $handler->handle($this->record);
    }

    /**
     * Set the minimum log level to WARNING and send in a NOTICE
     */
    public function testLevelsItShouldntHandle()
    {
        $handler = new CampfireHandler($this->campfire, Logger::EMERGENCY);
        $this->assertFalse($handler->handle($this->record));
    }

    /**
     * When the Campfire library throws an exception,
     * the handler should return false to indicate
     * that it didn't process the record.
     */
    public function testItReturnsFalseOnCampfireExceptions()
    {
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->will($this->returnValue($this->formatted_string));
        $this->campfire->expects($this->once())
                       ->method('send')
                       ->with($this->formatted_string)
                       ->will($this->throwException(new Exception()));
        $handler = new CampfireHandler($this->campfire, Logger::DEBUG);
        $handler->setFormatter($this->formatter);
        $this->assertFalse($handler->handle($this->record));
    }
}
