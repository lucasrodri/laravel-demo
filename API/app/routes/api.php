<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/message", function (Request $request) {
    $message = $_POST['message'];
    $mqService = new \App\Services\RabbitMQService();
    $mqService->publish($message);
    return view('rabbitmq.send');
});

Route::get("/receive", function (Request $request) {
    $mqService = new \App\Services\RabbitMQService();
    $html = $mqService->consume_without_wait();
    return $html;
});

Route::get('students', [\App\Http\Controllers\ApiController::class, 'getAllStudents']);
Route::get('students/{id}', [\App\Http\Controllers\ApiController::class, 'getStudent']);
Route::post('students', [\App\Http\Controllers\ApiController::class, 'createStudent']);
Route::put('students/{id}', [\App\Http\Controllers\ApiController::class, 'updateStudent']);
Route::delete('students/{id}', [\App\Http\Controllers\ApiController::class, 'deleteStudent']);