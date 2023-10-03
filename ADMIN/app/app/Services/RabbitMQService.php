<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
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
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare($queue, false, false, false, false);
        $channel->queue_bind($queue, 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / ".$queue.".\n";
        $channel->close();
        $connection->close();
    }
    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $continueConsuming = true;
        $callback = function ($msg) use (&$continueConsuming) {
            echo '[x] Received '. $msg->body. "\n";
            // Vão existir 5 modelos de mensagens, uma para cada tipo de operação
            // $msg->body = "CREATE -;- title -;- body // o separador é -;-
            // $msg->body = "UPDATE -;- id -;- title -;- body // o separador é -;-
            // $msg->body = "DELETE -;- id //numero do id
            // $msg->body = "READ -;- id //numero do id
            // $msg->body = "SHOW //vai mostrar todos os registos

            // Separe a mensagem em partes com base no separador '-;-'.
            $parts = explode(' -;- ', $msg->body);

            // Verifique o primeiro elemento para determinar a ação.
            $action = $parts[0];

            // Resto dos elementos contêm parâmetros.
            $params = array_slice($parts, 1);

            // Execute a ação apropriada com base no conteúdo da mensagem.
            switch ($action) {
                case 'CREATE':
                    $message = $this->createAction($params);
                    break;
                case 'UPDATE':
                    $message = $this->updateAction($params);
                    break;
                case 'DELETE':
                    $message = $this->deleteAction($params);
                    break;
                case 'READ':
                    $message = $this->readAction($params);
                    break;
                case 'SHOW':
                    $message = $this->showAction();
                    break;
                default:
                    warning('[x] Unknown action: '. $action);
                    break;
            }
            //avisar a api:
            //$this->publish($message, "api_queue");

            // if ($msg->body === 'quit') {
            //     echo ' [x] Quitting consumer', "\n";
            //     $continueConsuming = false;
            // }
        };
        $channel->queue_declare('administrador_queue', false, false, false, false);
        $channel->basic_consume('administrador_queue', '', false, true, false, false, $callback);
        echo 'Waiting for new message on administrador_queue', " \n";
        while ($continueConsuming && count($channel->callbacks) > 0) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    function createAction($params)
    {
        // Verifique se existem parâmetros suficientes.
        if (count($params) < 2) {
            warning('[x] Invalid CREATE AdminTable message format');
            return 'Invalid CREATE AdminTable message format';
        }
        
        // Chame o método da AdminTableController para criar um novo registro.
        $response = $this->adminTableController->store(request()->merge([
            'title' => $params[0],
            'body' => $params[1]
        ]));
    
        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 201) {
            echo '[x] Created AdminTable record: '.$response->content();
            return 'CREATE -;- AdminTable -;- '.$response->content();
        } else {
            error('[x] Failed to create AdminTable record');
        }
    }

    function updateAction($params)
    {
        // Verifique se existem parâmetros suficientes.
        if (count($params) < 3) {
            warning('[x] Invalid UPDATE AdminTable message format');
            return 'Invalid UPDATE AdminTable message format';
        }

        // Chame o método da AdminTableController para atualizar o registro.
        $response = $this->adminTableController->update($params[0], $params[1], $params[2]);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            echo '[x] Updated AdminTable record';
            return 'UPDATE -;- AdminTable -;- '.$response->content();
        } else {
            error('[x] Failed to update AdminTable record');
        }
    }

    function deleteAction($params)
    {
        // Verifique se existem parâmetros suficientes.
        if (count($params) < 1) {
            warning('[x] Invalid DELETE message format');
            return 'Invalid DELETE AdminTable message format';
        }

        // Obtenha o ID do registro a ser excluído.
        $id = $params[0];

        // Chame o método da AdminTableController para excluir o registro.
        $response = $this->adminTableController->destroy($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            echo '[x] Deleted AdminTable record';
            return 'DELETE -;- AdminTable -;- '.$response->content();
        } else {
            error('[x] Failed to delete AdminTable record');
        }
    }

    function readAction($params)
    {
        // Verifique se existem parâmetros suficientes.
        if (count($params) < 1) {
            warning('[x] Invalid READ message format');
            return 'Invalid READ AdminTable message format';
        }

        // Obtenha o ID do registro a ser lido.
        $id = $params[0];

        // Chame o método da AdminTableController para buscar o registro.
        $response = $this->adminTableController->show($id);

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            echo '[x] Found AdminTable record: ' . $response->getContent();
            return 'READ -;- AdminTable -;- '.$response->content();
        } else {
            error('[x] AdminTable not found');
        }
    }

    function showAction()
    {
        // Chame o método da AdminTableController para listar todos os registros.
        $response = $this->adminTableController->index();

        // Verifique a resposta e retorne a mensagem apropriada.
        if ($response->status() == 200) {
            echo '[x] Found all AdminTable records: '. $response->getContent();
            return 'SHOW -;- AdminTable -;- '.$response->content();
        } else {
            error('[x] Failed to fetch AdminTable records');
        }
    }
}
