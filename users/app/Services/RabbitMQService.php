<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
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
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
        $channel->queue_declare($queue, false, false, false, false);
        $channel->basic_consume($queue, '', false, true, false, false, $callback);
        echo 'Waiting for new message on ' . $queue, " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
