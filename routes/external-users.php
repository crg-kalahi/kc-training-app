<?php
use App\Http\Controllers\ExternalUsers\DashboardController;

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('/external-dashboard', [DashboardController::class,'index'])->name('external.dashboard');
});