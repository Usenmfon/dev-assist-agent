<?php

use App\Http\Controllers\DevAssistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/a2a/agent/dev_assist', [DevAssistController::class, 'handleWebhook']);
Route::get('/logs', [DevAssistController::class, 'logs']);

