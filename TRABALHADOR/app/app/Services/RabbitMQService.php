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
        $channel->queue_declare('trabalhador_queue', false, false, false, false);
        $channel->queue_bind('trabalhador_queue', 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / trabalhador_queue.\n";
        $channel->close();
        $connection->close();
    }
    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
        $channel->queue_declare('trabalhador_queue', false, false, false, false);
        $channel->basic_consume('trabalhador_queue', '', false, true, false, false, $callback);
        echo 'Waiting for new message on trabalhador_queue', " \n";
        while (count($channel->callbacks) > 0) {
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
        $channel->queue_declare('trabalhador_queue', false, false, false, false);
        $channel->basic_consume('trabalhador_queue', '', false, true, false, false, $callback);
        $channel->close();
        $connection->close();
        return $html;
    }
}
