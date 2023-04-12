<?php

use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\KeyResourcePersonController;
use App\Http\Controllers\KeyTrainingController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\OfficeRepresentativeController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingParticipantController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('training/evaluation', [TrainingController::class, 'StoreEvaluation'])->name('training.evaluation.post');
    Route::put('training-facilitators', [TrainingController::class, 'facilitateFacilitator'])->name('training.facilitators');
    Route::put('training-key-factors/{id}', [TrainingController::class, 'UpdateKeyFactors'])->name('training.key_factors');
    Route::put('training-resource-person', [TrainingController::class, 'UpdateResourcePerson'])->name('training.resource-person');
    Route::delete('training-resource-person', [TrainingController::class, 'RemoveResourcePerson'])->name('training.resource-person.destroy');
    Route::get('training/{id}/participants', [TrainingController::class, 'TGetParticipants'])->name('training.participants.index');
    Route::get('training/{id}/evaluations', [TrainingController::class, 'GetEvaluations'])->name('training.evaluations');
    Route::get('training/{id}/export-report', [TrainingController::class, 'ExportTrainingReport'])->name('training.export-report');
    Route::get('training/{id}/preview-report', [TrainingController::class, 'PreviewTrainingReport'])->name('training.preview-report');
    Route::apiResource('training-participant', TrainingParticipantController::class)->names('training.participant');
    Route::resource('training', TrainingController::class)->names('training');

    Route::group(['prefix' => 'configuration', 'as' => 'conf.'], function(){
        Route::get('/', [ConfigurationsController::class, 'Index'])->name('index');
        Route::get('/key-training', [ConfigurationsController::class, 'GetKeyTraining'])->name('key-training');
        Route::get('/key-resource-person', [ConfigurationsController::class, 'GetKeyResourcePerson'])->name('key-resource-person');
        Route::get('/key-learning', [ConfigurationsController::class, 'GetLearning'])->name('key-learning');
        Route::get('/lists-office-representative', [ConfigurationsController::class, 'GetOfficeRepresentative'])->name('lists-office-representative');

        Route::apiResource('training', KeyTrainingController::class)->names('training');
        Route::apiResource('learning', LearningController::class)->names('learning');
        Route::apiResource('resource-person', KeyResourcePersonController::class)->names('resource-person');
        Route::apiResource('office-representative', OfficeRepresentativeController::class)->names('office-representative');
    });
});

require __DIR__.'/auth.php';
