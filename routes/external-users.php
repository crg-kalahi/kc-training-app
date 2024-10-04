<?php
use App\Http\Controllers\ExternalUsers\DashboardController;
use App\Http\Controllers\ExternalUsers\TrainingController;

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('/external-dashboard', [DashboardController::class,'index'])->name('external.dashboard');
    Route::get('/my-trainings', [TrainingController::class,'index'])->name('external.my-trainings');
    Route::post('training/store-certificate-request', [TrainingController::class, 'StoreTrainingCertificateRequest'])->name('training.store-certificate-request');
    
});