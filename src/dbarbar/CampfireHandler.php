<?php

namespace dbarbar;

use Monolog\Handler\AbstractHandler;
use rcrowe\Campfire;
use Monolog\Logger;

/**
 * Provides a handler for Monolog that sends messages to a Campfire room.
 */
class CampfireHandler extends AbstractHandler
{
    protected $campfire;

    /**
     * @param Campfire $campfire An instance of the Campfire library.
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(Campfire $campfire, $level = Logger::DEBUG, $bubble = true)
    {
        $this->level = $level;
        $this->bubble = $bubble;
        $this->campfire = $campfire;
    }

    public function getCampfire()
    {
        return $this->campfire;
    }

    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        try {
            $this->campfire->send($this->getFormatter()->format($record));
        } catch (\Exception $e) {
            // eat the error but return false so the record will bubble.
            return false;
        }

        return $this->bubble;
    }
}
