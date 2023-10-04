<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use App\Services\RabbitMQService;

class ApiTrabController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/trabs",
     *     summary="Obtenha uma lista de trabalhadores",
     *     tags={"Trabs"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna uma lista de trabalhadores",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="SHOW"),
     *             @OA\Property(property="content", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function getAllTrabs()
    {
        // Gera um novo UUID
        $uuid = Uuid::uuid4();
        // Converte o UUID para sua representação em string
        $uuidString = $uuid->toString();
        // Criando a mensagem
        $message = [
            'request_UUID' => $uuidString,
            'action' => 'SHOW'
        ];
        $mqService = new RabbitMQService();
        $mqService->publish(json_encode($message), "trabalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    
    /**
     * @OA\Post(
     *     path="/api/trabs",
     *     summary="Criar um novo trab",
     *     description="Cria um novo trab com nome e texto.",
     *     tags={"Trabs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="João"),
     *             @OA\Property(property="body", type="string", example="Ciência da Computação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trab criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trab criado com sucesso"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação do trab",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro na criação do trab")
     *         )
     *     )
     * )
     */
    public function createTrab(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
    
        // Gera um novo UUID
        $uuid = Uuid::uuid4();
        // Converte o UUID para sua representação em string
        $uuidString = $uuid->toString();
        // Criando a mensagem
        $message = [
            'request_UUID' => $uuidString,
            'action' => 'CREATE',
            'title' => $validatedData['title'],
            'body' => $validatedData['body']
        ];
        $mqService = new RabbitMQService();
        $mqService->publish(json_encode($message), "trabalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Get(
     *     path="/api/trabs/{id}",
     *     summary="Obtém informações de um trabistrador",
     *     tags={"Trabs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do trabistrador a ser consultado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informações do trabistrador",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="READ"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trab não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trab não encontrado")
     *         )
     *     )
     * )
     */
    public function getTrab($id)
    {
        // Gera um novo UUID
        $uuid = Uuid::uuid4();
        // Converte o UUID para sua representação em string
        $uuidString = $uuid->toString();
        // Criando a mensagem
        $message = [
            'request_UUID' => $uuidString,
            'action' => 'READ',
            'id' => $id
        ];
        $mqService = new RabbitMQService();
        $mqService->publish(json_encode($message), "trabalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Put(
     *     path="/api/trabs/{id}",
     *     summary="Atualiza um trabistrador",
     *     tags={"Trabs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do trabistrador a ser atualizado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Dados do trabistrador para atualização",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Nome do trabistrador"),
     *             @OA\Property(property="body", type="string", description="Curso do trabistrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trab atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registros atualizados com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trab não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trab não encontrado")
     *         )
     *     )
     * )
     */
    public function updateTrab(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
    
        // Gera um novo UUID
        $uuid = Uuid::uuid4();
        // Converte o UUID para sua representação em string
        $uuidString = $uuid->toString();
        // Criando a mensagem
        $message = [
            'request_UUID' => $uuidString,
            'action' => 'UPDATE',
            'id' => $id,
            'title' => $validatedData['title'],
            'body' => $validatedData['body']
        ];
        $mqService = new RabbitMQService();
        $mqService->publish(json_encode($message), "trabalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Delete(
     *     path="/api/trabs/{id}",
     *     summary="Deleta um trabistrador",
     *     tags={"Trabs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do trabistrador a ser deletado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Trabistrador deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="DELETE"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trab não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trab não encontrado")
     *         )
     *     )
     * )
     */
    public function deleteTrab($id)
    {
        // Gera um novo UUID
        $uuid = Uuid::uuid4();
        // Converte o UUID para sua representação em string
        $uuidString = $uuid->toString();
        // Criando a mensagem
        $message = [
            'request_UUID' => $uuidString,
            'action' => 'DELETE',
            'id' => $id
        ];
        $mqService = new RabbitMQService();
        $mqService->publish(json_encode($message), "trabalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }
}
