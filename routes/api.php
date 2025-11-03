<?php

use App\Http\Controllers\DevAssistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dev-assist/logs', [DevAssistController::class, 'logs']);
Route::post('/dev-assist/webhook', [DevAssistController::class, 'handleWebhook']);

