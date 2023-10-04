<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Http\Controllers\TrabTableController;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

class RabbitMQService
{
    private $trabTableController;
    public function __construct()
    {
        $this->trabTableController = new TrabTableController();
    }
    public function publish($message, $queue = "trabalhador_queue")
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
        $continueConsuming = true;
        $callback = function ($msg) use (&$continueConsuming) {
            $messageData = json_decode($msg->body, true); // Decodifica o JSON para um array associativo
            info('[x] Received '. $messageData. "\n");
            
            // Exemplo de mensagem:
            // $msg->body = [
            //     'request_UUID' => 'id',
            //     'action' => 'CREATE', ['CREATE', 'UPDATE', 'DELETE', 'READ', 'SHOW']
            //     'id' => 'id', [somente para UPDATE, DELETE e READ]
            //     'title' => 'title', [somente para CREATE e UPDATE]
            //     'body' => 'body' [somente para CREATE e UPDATE]
            // ];

            $action = $messageData['action'];
            $request_UUID = $messageData['request_UUID'];
            $message = '';
            // Execute a ação apropriada com base no conteúdo da mensagem.
            switch ($action) {
                case 'CREATE':
                    $message = $this->createAction($messageData['title'], $messageData['body']);
                    break;
                case 'UPDATE':
                    $message = $this->updateAction($messageData['id'], $messageData['title'], $messageData['body']);
                    break;
                case 'DELETE':
                    $message = $this->deleteAction($messageData['id']);
                    break;
                case 'READ':
                    $message = $this->readAction($messageData['id']);
                    break;
                case 'SHOW':
                    $message = $this->showAction();
                    break;
                default:
                    warning('[x] Unknown action: '. $action. "\n");
                    break;
            }
            //avisar a api se mensagem foi bem sucedida ou não
            if ($message != '') {
                $this->publish($message, "api_queue_".$request_UUID);
            }
            // if ($messageData === 'quit') {
            //     info('[x] Quitting consumer\n';
            //     $continueConsuming = false;
            // }
        };
        $channel->queue_declare('trabalhador_queue', false, false, false, false);
        $channel->basic_consume('trabalhador_queue', '', false, true, false, false, $callback);
        info('Waiting for new message on trabalhador_queue\n');
        while ($continueConsuming && count($channel->callbacks) > 0) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    function createAction($title, $body)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($title) || !isset($body)) {
            warning('[x] Invalid CREATE TrabTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid CREATE TrabTable message format'
            ];
            return json_encode($message);
        }
        
        // Chame o método da TrabTableController para criar um novo registro.
        $response = $this->trabTableController->store(request()->merge([
            'title' => $title,
            'body' => $body
        ]));
    
        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 201) {
            info('[x] Created TrabTable record: '.$response->content().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to create TrabTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to create TrabTable record'
            ];
            return json_encode($message);
        }
    }

    function updateAction($id, $title, $body)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($id) || !isset($title) || !isset($body)) {
            warning('[x] Invalid UPDATE TrabTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid UPDATE TrabTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da TrabTableController para atualizar o registro.
        $response = $this->trabTableController->update($id, $title, $body);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Updated TrabTable record\n');
            else {
                warning('[x] ID TrabTable not found in UPDADE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to update TrabTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to update TrabTable record'
            ];
            return json_encode($message);
        }
    }

    function deleteAction($id)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($id)) {
            warning('[x] Invalid DELETE message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid DELETE TrabTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da TrabTableController para excluir o registro.
        $response = $this->trabTableController->destroy($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Deleted TrabTable record\n');
            else {
                warning('[x] ID TrabTable not found in DELETE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to delete TrabTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to delete TrabTable record'
            ];
            return json_encode($message);
        }
    }

    function readAction($id)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($id)) {
            warning('[x] Invalid READ message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid READ TrabTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da TrabTableController para buscar o registro.
        $response = $this->trabTableController->show($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Found TrabTable record: ' . $response->getContent().'\n');
            else {
                warning('[x] ID TrabTable not found in READ\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] TrabTable not found\n');
            $message = [
                'status' => 500,
                'content' => 'TrabTable not found'
            ];
            return json_encode($message);
        }
    }

    function showAction()
    {
        // Chame o método da TrabTableController para listar todos os registros.
        $response = $this->trabTableController->index();

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            info('[x] Found all TrabTable records: '. $response->getContent().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to fetch TrabTable records\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to fetch TrabTable records'
            ];
            return json_encode($message);
        }
    }
}