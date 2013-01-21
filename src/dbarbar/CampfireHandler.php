<?php

namespace dbarbar;

use Monolog\Handler\AbstractHandler;
use rcrowe\Campfire;
use Monolog\Logger;

class CampfireHandler extends AbstractHandler
{
    protected $campfire;

    /**
     * Campfire $config = array('subdomain' => '', 'room' => '', 'key' => '')
     * Room is numeric.
     * @param integer $level  The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
     * @param array   $config Pass in the required config params to initalise the Campfire library.
     */
    public function __construct($level = Logger::DEBUG, $bubble = true, Campfire $campfire)
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
