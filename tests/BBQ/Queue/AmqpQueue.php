<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\BBQ\Queue\tests\units;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpQueue extends \Eventio\BBQ\Queue\tests\units\AbstractQueue
{
    public function beforeTestMethod($method)
    {
        parent::beforeTestMethod($method);

        $config = include(TESTS_ROOT.'/config_amqp.php');
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);

        $amqpQueue = new \Wizacha\BBQ\Queue\AmqpQueue(
            parent::QUEUE_NAME,
            $connection
        );
        $this->bbq->registerQueue($amqpQueue);

        while($job = $amqpQueue->fetchJob(0)){
            $amqpQueue->finalizeJob($job);
        }
    }
}
