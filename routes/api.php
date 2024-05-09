<?php

use Illuminate\Support\Facades\Route;

Route::post('/date/check', [\App\Http\Controllers\Api\DateController::class, 'check'])->name('api.post.date.check');
Route::post('/ticket/calc', [\App\Http\Controllers\Api\TicketController::class, 'calcEndTime'])->name('api.post.ticket.calcEndTime');
