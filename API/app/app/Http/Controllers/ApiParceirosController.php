<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use App\Services\RabbitMQService;

class ApiParceirosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/parceiros",
     *     summary="Obtenha uma lista de parceiros",
     *     tags={"Parceiros"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna uma lista de parceiros",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="SHOW"),
     *             @OA\Property(property="content", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function getAllParceiros()
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
        $mqService->publish(json_encode($message), "parceirosalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    
    /**
     * @OA\Post(
     *     path="/api/parceiros",
     *     summary="Criar um novo parceiros",
     *     description="Cria um novo parceiros com nome e texto.",
     *     tags={"Parceiros"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="João"),
     *             @OA\Property(property="body", type="string", example="Ciência da Computação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Parceiros criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parceiros criado com sucesso"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação do parceiros",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro na criação do parceiros")
     *         )
     *     )
     * )
     */
    public function createParceiros(Request $request)
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
        $mqService->publish(json_encode($message), "parceirosalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Get(
     *     path="/api/parceiros/{id}",
     *     summary="Obtém informações de um parceirosistrador",
     *     tags={"Parceiros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do parceirosistrador a ser consultado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informações do parceirosistrador",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="READ"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parceiros não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parceiros não encontrado")
     *         )
     *     )
     * )
     */
    public function getParceiros($id)
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
        $mqService->publish(json_encode($message), "parceirosalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Put(
     *     path="/api/parceiros/{id}",
     *     summary="Atualiza um parceirosistrador",
     *     tags={"Parceiros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do parceirosistrador a ser atualizado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Dados do parceirosistrador para atualização",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Nome do parceirosistrador"),
     *             @OA\Property(property="body", type="string", description="Curso do parceirosistrador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parceiros atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registros atualizados com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parceiros não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parceiros não encontrado")
     *         )
     *     )
     * )
     */
    public function updateParceiros(Request $request, $id)
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
        $mqService->publish(json_encode($message), "parceirosalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }

    /**
     * @OA\Delete(
     *     path="/api/parceiros/{id}",
     *     summary="Deleta um parceirosistrador",
     *     tags={"Parceiros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do parceirosistrador a ser deletado",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Parceirosistrador deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="request_UUID", type="string", example="UUID"),
     *             @OA\Property(property="action", type="string", example="DELETE"),
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parceiros não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parceiros não encontrado")
     *         )
     *     )
     * )
     */
    public function deleteParceiros($id)
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
        $mqService->publish(json_encode($message), "parceirosalhador_queue");
        $response_message = $mqService->consume_queue("api_queue_".$uuidString);

        $mqService->deleteQueue("api_queue_".$uuidString);

        return response($response_message['content'], $response_message['status']);
    }
}
