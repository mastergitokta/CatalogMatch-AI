<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('telegram/webhook/{token}', TelegramWebhookController::class)
    ->name('telegram.webhook');
