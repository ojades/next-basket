<?php

namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RabbitMQService extends Command
{
    private const DEFAULT = 'default';

    public function publish($message, $queue = self::DEFAULT)
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );
        $channel = $connection->channel();
        $channel->exchange_declare($queue . '_exchange', 'direct', false, false, false);
        $channel->queue_declare($queue, false, false, false, false);
        $channel->queue_bind($queue, $queue . '_exchange', $queue . '_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, $queue . '_exchange', $queue . '_key');
        echo " [x] Sent $message to {$queue}_exchange / $queue.\n";
        $channel->close();
        $connection->close();
    }
    public function consume($queue = self::DEFAULT)
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        $channel->basic_qos(null, 1, null);
        echo 'Waiting for new message on ' . $queue . '...', " \n";

        $callback = function ($msg) {
            try {
                $this->onMessage($msg->body);
            } catch (\Exception $e) {
                Log::log('rabbitmq', $e->getMessage());
                Log::log('rabbitmq', $e->getTraceAsString());
            }
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            try {
                $channel->wait();
            } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
                // Do nothing
            } catch (\PhpAmqpLib\Exception\AMQPRuntimeException $e) {
                // Don nothing
            }
        }
        $channel->close();
        $connection->close();
    }

    abstract public function onMessage($msg);
}
