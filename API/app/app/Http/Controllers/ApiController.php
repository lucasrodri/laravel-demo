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
     *     @OA\Response(response="201", description="Display a listing of the resource")
     * )
     */
    public function createStudent(Request $request)
    {
        $student = new Student;
        $student->name = $request->name;
        $student->course = $request->course;
        $student->save();

        return response()->json([
            "message" => "student record created"
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
        if (Student::where('id', $id)->exists()) {
            $student = Student::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($student, 200);
        } else {
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
