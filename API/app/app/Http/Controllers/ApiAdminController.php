<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use App\Services\RabbitMQService;

class ApiAdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admins",
     *     summary="Obtenha uma lista de administradores",
     *     tags={"Admins"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna uma lista de administradores",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="SHOW"),
     *             @OA\Property(property="content", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function getAllAdmins()
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
        $mqService->publish(json_encode($message), "administrador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    
    /**
     * @OA\Post(
     *     path="/api/admins",
     *     summary="Criar um novo admin",
     *     description="Cria um novo admin com nome e texto.",
     *     tags={"Admins"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="João"),
     *             @OA\Property(property="body", type="string", example="Ciência da Computação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin criado com sucesso"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação do admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro na criação do admin")
     *         )
     *     )
     * )
     */
    public function createAdmin(Request $request)
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
        $mqService->publish(json_encode($message), "administrador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Get(
     *     path="/api/admins/{id}",
     *     summary="Obtém informações de um administrador",
     *     tags={"Admins"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do administrador a ser consultado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informações do administrador",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="READ"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin não encontrado")
     *         )
     *     )
     * )
     */
    public function getAdmin($id)
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
        $mqService->publish(json_encode($message), "administrador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Put(
     *     path="/api/admins/{id}",
     *     summary="Atualiza um administrador",
     *     tags={"Admins"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do administrador a ser atualizado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Dados do administrador para atualização",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Nome do administrador"),
     *             @OA\Property(property="body", type="string", description="Curso do administrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registros atualizados com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin não encontrado")
     *         )
     *     )
     * )
     */
    public function updateAdmin(Request $request, $id)
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
        $mqService->publish(json_encode($message), "administrador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Delete(
     *     path="/api/admins/{id}",
     *     summary="Deleta um administrador",
     *     tags={"Admins"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do administrador a ser deletado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Administrador deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="DELETE"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin não encontrado")
     *         )
     *     )
     * )
     */
    public function deleteAdmin($id)
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
        $mqService->publish(json_encode($message), "administrador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }
}
