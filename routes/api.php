<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Mural de Avisos
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);

    // Documentos
    Route::get('/docs', [DocController::class, 'getAll']);

    // Livro de ocorrencias
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);
    Route::post('/warning', [WarningController::class, 'setWarning']);

    // Boletos
    Route::get('/billets', [BilletController::class, 'getAll']);

    // Achados e Perdidos
    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);

    // Unidade
    Route::get('/units/{id}', [UnitController::class, 'getInfo']);
    Route::post('/units/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/units/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/units/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/units/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/units/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/units/{id}/removepet', [UnitController::class, 'removePet']);

    // Reservas
    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);

    Route::get('/resevation/{id}/desableddates', [ReservationController::class, 'getDesabledDates']);
    Route::get('/resevation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::delete('/myreservation/{id}', [ReservationController::class, 'delMyReservation']);
});
