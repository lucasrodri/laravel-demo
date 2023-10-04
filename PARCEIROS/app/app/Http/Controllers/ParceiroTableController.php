<?php

namespace App\Http\Controllers;

use App\Models\ParceiroTable;
use Illuminate\Http\Request;

class ParceiroTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parceiroTable = ParceiroTable::all();
        // Responda com o status 200 e inclua o registro criado no corpo da resposta.
        return response()->json($parceiroTable, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $parceiroTable = ParceiroTable::create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        // Responda com o status 201 (Created) e inclua o registro criado no corpo da resposta.
        return response()->json($parceiroTable, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Encontre o registro pelo ID.
        $parceiroTable = ParceiroTable::find($id);
        if ($parceiroTable) {
            return response()->json($parceiroTable, 200);
        } else {
            return response()->json(['message' => 'ParceiroTable not found', 'id' => $id], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $title, $body)
    {
        $parceiroTable = ParceiroTable::find($id);
        if (!$parceiroTable) {
            return response()->json(['message' => 'ParceiroTable not found', 'id' => $id], 404);
        }
        // Atualize o registro.
        $parceiroTable->update([
            'title' => $title,
            'body' => $body
        ]);
        // Responda com o status 200 (OK) e inclua o registro atualizado no corpo da resposta.
        return response()->json($parceiroTable, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontre o registro pelo ID.
        $parceiroTable = ParceiroTable::find($id);
        if ($parceiroTable) {
            $parceiroTable->delete();
            return response()->json(['message' => 'ParceiroTable deleted', 'id' => $id], 200);
        } else {
            echo ' [x] ParceiroTable not found', "\n";
            return response()->json(['message' => 'ParceiroTable not found', 'id' => $id], 404);
        }
    }
}
