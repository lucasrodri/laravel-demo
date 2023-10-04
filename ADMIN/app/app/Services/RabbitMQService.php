<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Http\Controllers\AdminTableController;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

class RabbitMQService
{
    private $adminTableController;
    public function __construct()
    {
        $this->adminTableController = new AdminTableController();
    }
    public function publish($message, $queue = "administrador_queue")
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
        $channel->queue_declare('administrador_queue', false, false, false, false);
        $channel->basic_consume('administrador_queue', '', false, true, false, false, $callback);
        info('Waiting for new message on administrador_queue\n');
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
            warning('[x] Invalid CREATE AdminTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid CREATE AdminTable message format'
            ];
            return json_encode($message);
        }
        
        // Chame o método da AdminTableController para criar um novo registro.
        $response = $this->adminTableController->store(request()->merge([
            'title' => $title,
            'body' => $body
        ]));
    
        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 201) {
            info('[x] Created AdminTable record: '.$response->content().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to create AdminTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to create AdminTable record'
            ];
            return json_encode($message);
        }
    }

    function updateAction($id, $title, $body)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($id) || !isset($title) || !isset($body)) {
            warning('[x] Invalid UPDATE AdminTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid UPDATE AdminTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da AdminTableController para atualizar o registro.
        $response = $this->adminTableController->update($id, $title, $body);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Updated AdminTable record\n');
            else {
                warning('[x] ID AdminTable not found in UPDADE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to update AdminTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to update AdminTable record'
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
                'content' => 'Invalid DELETE AdminTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da AdminTableController para excluir o registro.
        $response = $this->adminTableController->destroy($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Deleted AdminTable record\n');
            else {
                warning('[x] ID AdminTable not found in DELETE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to delete AdminTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to delete AdminTable record'
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
                'content' => 'Invalid READ AdminTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da AdminTableController para buscar o registro.
        $response = $this->adminTableController->show($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Found AdminTable record: ' . $response->getContent().'\n');
            else {
                warning('[x] ID AdminTable not found in READ\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] AdminTable not found\n');
            $message = [
                'status' => 500,
                'content' => 'AdminTable not found'
            ];
            return json_encode($message);
        }
    }

    function showAction()
    {
        // Chame o método da AdminTableController para listar todos os registros.
        $response = $this->adminTableController->index();

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            info('[x] Found all AdminTable records: '. $response->getContent().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to fetch AdminTable records\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to fetch AdminTable records'
            ];
            return json_encode($message);
        }
    }
}