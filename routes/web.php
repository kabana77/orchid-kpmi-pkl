<?php

use App\Http\Controllers\Site\BusinessController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\MemberController;
use App\Http\Controllers\Site\OpportunityController;
use Illuminate\Support\Facades\Route;

Route::name('public.')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::resource('anggota', MemberController::class)
        ->only(['index', 'show'])
        ->parameters(['anggota' => 'member']);

    Route::resource('usaha', BusinessController::class)
        ->only(['index', 'show'])
        ->parameters(['usaha' => 'business']);

    Route::resource('peluang', OpportunityController::class)
        ->only(['index', 'show'])
        ->parameters(['peluang' => 'opportunity']);
});
