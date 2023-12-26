<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\VacationTypeController;
use App\Http\Controllers\ReasonController;
use Illuminate\Support\Facades\Route;

Route::post('/demande-conge', [VacationController::class, 'create']);
Route::get('/demande-conge', [VacationController::class, 'getAll']);
Route::get('/demande-conge/{id}', [VacationController::class, 'getVacationRequest']);
Route::get('/demande-conge/employee/{id}', [VacationController::class, 'getVacationRequestByEmployeeId']);
Route::patch('/demande-conge/{id}', [VacationController::class, 'update']);
Route::delete('/demande-conge/{id}', [VacationController::class, 'deleteVacationRequest']);
Route::get('/employee', [EmployeeController::class, 'getEmployees']);
Route::get('/solde-conge/employee/{id}', [EmployeeController::class, 'getVacationBalance']);
Route::get('/reason', [ReasonController::class, 'getReasons']);
Route::get('/vacation-type', [VacationTypeController::class, 'getVacationTypes']);