<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\BBQ\Queue;

use \Eventio\BBQ\Queue\AbstractQueue;
use \Eventio\BBQ\Job\JobInterface;
use \Eventio\BBQ\Job\Payload\JobPayloadInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Wizacha\BBQ\Job\AmqpJob;

class AmqpQueue extends AbstractQueue
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    public function __construct($id, AMQPStreamConnection $connection)
    {
        $this->channel = $connection->channel();

        parent::__construct($id);
    }

    protected function init()
    {

    }

    /**
     * Retrieve one Job
     *
     * @param int|null $timeout (NOT SUPPORTED) Waiting time before closing request if no Job present in stack
     * @return AmqpJob|null
     */
    public function fetchJob($timeout = null)
    {
        $result = $this->channel->basic_get($this->id);

        if (!$result) {
            return null;
        }

        $job = new AmqpJob(unserialize($result->body), $result);
        $job->setQueue($this);

        $this->lockJob($job);

        return $job;
    }

    /**
     * Delete done Job
     * @param $job AmqpJob
     * @return bool
     */
    public function finalizeJob(JobInterface $job)
    {
        $this->channel->basic_ack($job->getAmqpResource()->delivery_info['delivery_tag']);
        $this->deleteLockedJob($job);

        return true;
    }

    /**
     * Create new Job
     * @return bool
     */
    public function pushJob(JobPayloadInterface $jobPayload)
    {
        $message = new AMQPMessage(serialize($jobPayload));
        $this->channel->basic_publish($message, '', $this->id);

        return true;
    }

    /**
     * Abandon Job and make him visible for other consumer
     * @param $job AmqpJob
     * @return bool
     */
    public function releaseJob(JobInterface $job)
    {
        // Requeue false to allow dead letter queue
        $this->channel->basic_nack($job->getAmqpResource()->delivery_info['delivery_tag'], false, false);
        $this->deleteLockedJob($job);

        return true;
    }
}
