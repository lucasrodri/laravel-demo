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

Route::get('students', [\App\Http\Controllers\ApiController::class, 'getAllStudents']);
Route::get('students/{id}', [\App\Http\Controllers\ApiController::class, 'getStudent']);
Route::post('students', [\App\Http\Controllers\ApiController::class, 'createStudent']);
Route::put('students/{id}', [\App\Http\Controllers\ApiController::class, 'updateStudent']);
Route::delete('students/{id}', [\App\Http\Controllers\ApiController::class, 'deleteStudent']);

# Rotas para o CRUD de elementos do microserviço de administrador
Route::get('admins', [\App\Http\Controllers\ApiAdminController::class, 'getAllAdmins']);
Route::get('admins/{id}', [\App\Http\Controllers\ApiAdminController::class, 'getAdmin']);
Route::post('admins', [\App\Http\Controllers\ApiAdminController::class, 'createAdmin']);
Route::put('admins/{id}', [\App\Http\Controllers\ApiAdminController::class, 'updateAdmin']);
Route::delete('admins/{id}', [\App\Http\Controllers\ApiAdminController::class, 'deleteAdmin']);

# Rotas para o CRUD de elementos do microserviço de trabalhador
Route::get('trabs', [\App\Http\Controllers\ApiTrabController::class, 'getAllTrabs']);
Route::get('trabs/{id}', [\App\Http\Controllers\ApiTrabController::class, 'getTrab']);
Route::post('trabs', [\App\Http\Controllers\ApiTrabController::class, 'createTrab']);
Route::put('trabs/{id}', [\App\Http\Controllers\ApiTrabController::class, 'updateTrab']);
Route::delete('trabs/{id}', [\App\Http\Controllers\ApiTrabController::class, 'deleteTrab']);

# Rotas para o CRUD de elementos do microserviço de parceiros
Route::get('parceiros', [\App\Http\Controllers\ApiParceirosController::class, 'getAllParceiross']);
Route::get('parceiros/{id}', [\App\Http\Controllers\ApiParceirosController::class, 'getParceiros']);
Route::post('parceiros', [\App\Http\Controllers\ApiParceirosController::class, 'createParceiros']);
Route::put('parceiros/{id}', [\App\Http\Controllers\ApiParceirosController::class, 'updateParceiros']);
Route::delete('parceiros/{id}', [\App\Http\Controllers\ApiParceirosController::class, 'deleteParceiros']);