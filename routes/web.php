<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\KeyResourcePersonController;
use App\Http\Controllers\KeyTrainingController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\OfficeRepresentativeController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingParticipantController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
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

Route::get('/test', function(){ return view('emails.cert_participation', [
    'title' => 'test',
    'fullname' => 'test',
    'venue' => 'test',
]); });
Route::get('/thank-you-response', function(){ return view('thanks'); })->name('thanks.response');
Route::get('training/certificate/participation', [CertificateController::class, 'participation'])->name('public.cert.participant');
Route::post('training/evaluation/public', [TrainingController::class, 'PublicEvaluationFormStore'])->name('public.training.evaluation.post');
Route::get('training/{id}/evaluation-response/public', [TrainingController::class, 'PublicEvaluationForm'])->name('public.training.evaluation-response');

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('/dashboard', function () {

        $results = DB::select("SELECT
                            month,
                            mname,
                            yr,
                            total_trainings
                            FROM (
                                SELECT
                                DATE_FORMAT(date_from, '%Y-%m') AS month, 
                                MONTHNAME(date_from) AS mname,
                                YEAR(date_from) AS yr,
                                COUNT(*) AS total_trainings
                                FROM
                                trainings
                                GROUP BY
                                YEAR(date_from),
                                DATE_FORMAT(date_from, '%Y-%m'),
                                MONTHNAME(date_from)
                                ORDER BY
                                DATE_FORMAT(date_from, '%Y-%m') DESC
                                LIMIT 12
                            ) AS subquery
                            ORDER BY
                            month ASC
                        ");
            $trainings = DB::select("SELECT JSON_OBJECT(
                                    'title', title,
                                    'location', venue,
                                    'time', JSON_OBJECT('start', date_from, 'end', date_to),
                                    'color', 'green',       -- static value or you can set based on logic
                                    'isEditable', true,     -- static value or you can set based on logic
                                    'id', id
                                ) AS result
                                FROM trainings
                                ORDER BY date_from DESC;");
            $formattedTrainings = array_map(fn($row) => json_decode($row->result, true), $trainings);
            
        return Inertia::render('Dashboard', [
            'participants' => DB::table('participant_lists_view')->groupBy('full_name', 'is_internal')->get(),
            'earliest_day_training' => DB::table('event'),
            'plByMonth' => $results,
            'events' => $formattedTrainings
        ]);
    })->name('dashboard');

    Route::post('training/evaluation', [TrainingController::class, 'StoreEvaluation'])->name('training.evaluation.post');
    Route::put('training-facilitators', [TrainingController::class, 'facilitateFacilitator'])->name('training.facilitators');
    Route::put('training-key-factors/{id}', [TrainingController::class, 'UpdateKeyFactors'])->name('training.key_factors');
    Route::put('training-resource-person', [TrainingController::class, 'UpdateResourcePerson'])->name('training.resource-person');
    Route::delete('training-resource-person', [TrainingController::class, 'RemoveResourcePerson'])->name('training.resource-person.destroy');
    Route::get('training/{id}/participants', [TrainingController::class, 'TGetParticipants'])->name('training.participants.index');
    Route::get('training/{id}/evaluations', [TrainingController::class, 'GetEvaluations'])->name('training.evaluations');
    Route::get('training/{id}/evaluation-response', [TrainingController::class, 'GetEvaluationResponse'])->name('training.evaluation-response');
    Route::put('evaluation-response/{id}/update', [TrainingController::class, 'UpdateEvaluationResponse'])->name('training.evaluation-response.update');
    Route::get('evaluation-response/{id}/show', [TrainingController::class, 'ShowEvaluationResponse'])->name('training.evaluation-response.show');
    Route::delete('evaluation-response/{id}/delete', [TrainingController::class, 'RemoveEvaluationResponse'])->name('training.evaluation-response.delete');
    Route::get('training/{id}/export-report', [TrainingController::class, 'ExportTrainingReport'])->name('training.export-report');
    Route::get('training/{id}/export-participants', [TrainingController::class, 'ExportTrainingParticipants'])->name('training.export-participants');
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
