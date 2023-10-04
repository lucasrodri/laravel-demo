<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function publish($message, $queue = "api_queue")
    {
        info("Enviando para a FILA $queue\n");
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare($queue, false, false, false, false);
        $channel->queue_bind($queue, 'test_exchange', $queue); // Usar o nome da fila como chave de roteamento
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', $queue); // Usar o nome da fila como chave de roteamento
        info("[x] Sent $message to test_exchange / $queue.\n");
        $channel->close();
        $connection->close();
    }

    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $callback = function ($msg) {
            info('[x] Received '.$msg->body."\n");
        };
        $channel->queue_declare('api_queue', false, false, false, false);
        $channel->basic_consume('api_queue', '', false, true, false, false, $callback);
        info('Waiting for new message on api_queue\n');
        while ($channel->is_consuming()) {
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
        $channel->queue_declare('api_queue', false, false, false, false);
        $channel->basic_consume('api_queue', '', false, true, false, false, $callback);
        $channel->close();
        $connection->close();
        return $html;
    }
    public function deleteQueue($queue)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->queue_delete($queue);
        $channel->close();
        $connection->close();
    }

    public function consume_queue($queue)
    {
        $continueConsuming = true;
        $response = '';
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $callback = function ($msg) use (&$continueConsuming, &$response) {
            $response = json_decode($msg->body, true);
            $continueConsuming = false;
        };
        $channel->queue_declare($queue, false, false, false, false);
        $channel->basic_consume($queue, '', false, true, false, false, $callback);
        while ($continueConsuming) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return $response;
    }
}
