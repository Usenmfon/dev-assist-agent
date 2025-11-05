<?php

use App\Http\Controllers\DevAssistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/dev_assit/webhook', [DevAssistController::class, 'handleWebhook']);
Route::get('/logs', [DevAssistController::class, 'logs']);

