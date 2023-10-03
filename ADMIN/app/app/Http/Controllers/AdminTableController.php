<?php

namespace App\Http\Controllers;

use App\Models\AdminTable;
use Illuminate\Http\Request;

class AdminTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adminTable = AdminTable::all();
        // Responda com o status 200 e inclua o registro criado no corpo da resposta.
        return response()->json($adminTable, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $adminTable = AdminTable::create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        // Responda com o status 201 (Created) e inclua o registro criado no corpo da resposta.
        return response()->json($adminTable, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Encontre o registro pelo ID.
        $adminTable = AdminTable::find($id);
        if ($adminTable) {
            return response()->json($adminTable, 200);
        } else {
            return response()->json(['message' => 'AdminTable not found', 'id' => $id], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $title, $body)
    {
        $adminTable = AdminTable::find($id);
        if (!$adminTable) {
            return response()->json(['message' => 'AdminTable not found', 'id' => $id], 404);
        }
        // Atualize o registro.
        $adminTable->update([
            'title' => $title,
            'body' => $body
        ]);
        // Responda com o status 200 (OK) e inclua o registro atualizado no corpo da resposta.
        return response()->json($adminTable, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontre o registro pelo ID.
        $adminTable = AdminTable::find($id);
        if ($adminTable) {
            $adminTable->delete();
            return response()->json(['message' => 'AdminTable deleted', 'id' => $id], 200);
        } else {
            echo ' [x] AdminTable not found', "\n";
            return response()->json(['message' => 'AdminTable not found', 'id' => $id], 404);
        }
    }
}
