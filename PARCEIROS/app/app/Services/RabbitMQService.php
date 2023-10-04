<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Http\Controllers\ParceiroTableController;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

class RabbitMQService
{
    private $parceirosTableController;
    public function __construct()
    {
        $this->parceirosTableController = new ParceiroTableController();
    }
    public function publish($message, $queue = "parceiros_queue")
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
        $channel->queue_declare('parceiros_queue', false, false, false, false);
        $channel->basic_consume('parceiros_queue', '', false, true, false, false, $callback);
        info('Waiting for new message on parceiros_queue\n');
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
            warning('[x] Invalid CREATE ParceirosTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid CREATE ParceirosTable message format'
            ];
            return json_encode($message);
        }
        
        // Chame o método da ParceirosTableController para criar um novo registro.
        $response = $this->parceirosTableController->store(request()->merge([
            'title' => $title,
            'body' => $body
        ]));
    
        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 201) {
            info('[x] Created ParceirosTable record: '.$response->content().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to create ParceirosTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to create ParceirosTable record'
            ];
            return json_encode($message);
        }
    }

    function updateAction($id, $title, $body)
    {
        // Verifique se os parâmetros são válidos.
        if (!isset($id) || !isset($title) || !isset($body)) {
            warning('[x] Invalid UPDATE ParceirosTable message format\n');
            $message = [
                'status' => 400,
                'content' => 'Invalid UPDATE ParceirosTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da ParceirosTableController para atualizar o registro.
        $response = $this->parceirosTableController->update($id, $title, $body);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Updated ParceirosTable record\n');
            else {
                warning('[x] ID ParceirosTable not found in UPDADE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to update ParceirosTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to update ParceirosTable record'
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
                'content' => 'Invalid DELETE ParceirosTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da ParceirosTableController para excluir o registro.
        $response = $this->parceirosTableController->destroy($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Deleted ParceirosTable record\n');
            else {
                warning('[x] ID ParceirosTable not found in DELETE\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to delete ParceirosTable record\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to delete ParceirosTable record'
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
                'content' => 'Invalid READ ParceirosTable message format'
            ];
            return json_encode($message);
        }

        // Chame o método da ParceirosTableController para buscar o registro.
        $response = $this->parceirosTableController->show($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200 || $response->status() == 404) {
            if ($response->status() == 200)
                info('[x] Found ParceirosTable record: ' . $response->getContent().'\n');
            else {
                warning('[x] ID ParceirosTable not found in READ\n');
            }
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] ParceirosTable not found\n');
            $message = [
                'status' => 500,
                'content' => 'ParceirosTable not found'
            ];
            return json_encode($message);
        }
    }

    function showAction()
    {
        // Chame o método da ParceirosTableController para listar todos os registros.
        $response = $this->parceirosTableController->index();

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            info('[x] Found all ParceirosTable records: '. $response->getContent().'\n');
            $message = [
                'status' => $response->status(),
                'content' => $response->content()
            ];
            return json_encode($message);
        } else {
            error('[x] Failed to fetch ParceirosTable records\n');
            $message = [
                'status' => 500,
                'content' => 'Failed to fetch ParceirosTable records'
            ];
            return json_encode($message);
        }
    }
}