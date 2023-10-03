<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Swagger API documentation",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="lucasrc.rodri@gmail.com"
 *    )
 * )
 */
class ApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/students",
     *     @OA\Response(response="200", description="Display a listing of the resource")
     * )
     */
    public function getAllStudents()
    {
        $students = Student::get()->toJson(JSON_PRETTY_PRINT);
        return response($students, 200);
    }

    
    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="Criar um novo estudante",
     *     description="Cria um novo estudante com nome e curso.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="João"),
     *             @OA\Property(property="course", type="string", example="Ciência da Computação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estudante criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estudante criado com sucesso"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação do estudante",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro na criação do estudante")
     *         )
     *     )
     * )
     */
    public function createStudent(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'course' => 'required|string',
        ]);
    
        $student = Student::create([
            'name' => $validatedData['name'],
            'course' => $validatedData['course'],
        ]);
    
        return response()->json([
            "message" => "Estudante criado com sucesso",
            "id" => $student->id
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}",
     *     @OA\Response(response="200", description="Display a listing of the resource")
     * )
     */
    public function getStudent($id)
    {
        try {
            $student = Student::find($id);
            if ($student) {
                return response()->json($student, 200);
            } else {
                return response()->json([
                    "message" => "Student not found"
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Student not found"
            ], 404);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/students/{id}",
     *     @OA\Response(response="200", description="Display a listing of the resource")
     * )
     */
    public function updateStudent(Request $request, $id)
    {
        if (Student::where('id', $id)->exists()) {
            $student = Student::find($id);

            $student->name = is_null($request->name) ? $student->name : $request->name;
            $student->course = is_null($request->course) ? $student->course : $request->course;
            $student->save();

            return response()->json([
                "message" => "records updated successfully"
            ], 200);
        } else {
            return response()->json([
                "message" => "Student not found"
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     @OA\Response(response="202", description="Display a listing of the resource")
     * )
     */
    public function deleteStudent($id)
    {
        if (Student::where('id', $id)->exists()) {
            $student = Student::find($id);
            $student->delete();

            return response()->json([
                "message" => "records deleted"
            ], 202);
        } else {
            return response()->json([
                "message" => "Student not found"
            ], 404);
        }
    }
}
