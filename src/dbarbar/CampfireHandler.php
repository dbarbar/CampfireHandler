<?php

namespace dbarbar;

use Monolog\Handler\AbstractHandler;
use rcrowe\Campfire;
use Monolog\Logger;

class CampfireHandler extends AbstractHandler
{
  protected $campfire;

  /**
   *
   *
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

    /**
     * Record looks like this:
     * return array(
     *       'message' => $message,
     *       'context' => $context,
     *       'level' => $level,
     *       'level_name' => Logger::getLevelName($level),
     *       'channel' => 'test',
     *       'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
     *       'extra' => array(),
     *   );
     */
    $formatted_message = $record['channel'] . ' - ' . $record['level_name'] . ' - ' . $record['datetime'] . ' - ' . $record['message'];
    $this->campfire->send($formatted_message);

    return $this->bubble;
  }
}
