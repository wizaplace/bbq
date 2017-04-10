<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\BBQ\Job;

use \Eventio\BBQ\Job\Job;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpJob extends Job
{
    /**
     * @var AMQPMessage
     */
    protected $amqpResource;

    public function __construct($payload, AMQPMessage $amqpResource)
    {
        $this->amqpResource = $amqpResource;
        parent::__construct($payload);
    }

    /**
     * @return AMQPMessage
     */
    public function getAmqpResource()
    {
        return $this->amqpResource;
    }
}
