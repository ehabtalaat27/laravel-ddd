<?php

use App\User\Presentation\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//users
Route::apiResource('users', UserController::class)->except('update');

Route::patch('users/{id}/deactivate', [UserController::class, 'deactivate']);