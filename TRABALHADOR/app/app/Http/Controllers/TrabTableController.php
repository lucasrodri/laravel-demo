<?php

namespace App\Http\Controllers;

use App\Models\TrabTable;
use Illuminate\Http\Request;

class TrabTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trabTable = TrabTable::all();
        // Responda com o status 200 e inclua o registro criado no corpo da resposta.
        return response()->json($trabTable, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $trabTable = TrabTable::create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        // Responda com o status 201 (Created) e inclua o registro criado no corpo da resposta.
        return response()->json($trabTable, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Encontre o registro pelo ID.
        $trabTable = TrabTable::find($id);
        if ($trabTable) {
            return response()->json($trabTable, 200);
        } else {
            return response()->json(['message' => 'TrabTable not found', 'id' => $id], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $title, $body)
    {
        $trabTable = TrabTable::find($id);
        if (!$trabTable) {
            return response()->json(['message' => 'TrabTable not found', 'id' => $id], 404);
        }
        // Atualize o registro.
        $trabTable->update([
            'title' => $title,
            'body' => $body
        ]);
        // Responda com o status 200 (OK) e inclua o registro atualizado no corpo da resposta.
        return response()->json($trabTable, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontre o registro pelo ID.
        $trabTable = TrabTable::find($id);
        if ($trabTable) {
            $trabTable->delete();
            return response()->json(['message' => 'TrabTable deleted', 'id' => $id], 200);
        } else {
            echo ' [x] TrabTable not found', "\n";
            return response()->json(['message' => 'TrabTable not found', 'id' => $id], 404);
        }
    }
}
