<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function publish($message)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare('admin_queue', false, false, false, false);
        $channel->queue_bind('admin_queue', 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / admin_queue.\n";
        $channel->close();
        $connection->close();
    }
    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $continueConsuming = true;
        $callback = function ($msg) use (&$continueConsuming) {
            echo ' [x] Received ', $msg->body, "\n";
            if ($msg->body === 'quit') {
                echo ' [x] Quitting consumer', "\n";
                $continueConsuming = false;
            }
        };
        $channel->queue_declare('admin_queue', false, false, false, false);
        $channel->basic_consume('admin_queue', '', false, true, false, false, $callback);
        echo 'Waiting for new message on admin_queue', " \n";
        while ($continueConsuming && count($channel->callbacks) > 0) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
    public function consume_without_wait()
    {
        $html = '';
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $callback = function ($msg) use (&$html) {
            $html .= ' [x] Received ' . $msg->body . "\n";
        };
        echo $html;
        $channel->queue_declare('admin_queue', false, false, false, false);
        $channel->basic_consume('admin_queue', '', false, true, false, false, $callback);
        $channel->close();
        $connection->close();
        return $html;
    }
}
