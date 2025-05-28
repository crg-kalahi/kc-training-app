<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\KeyResourcePersonController;
use App\Http\Controllers\KeyTrainingController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\OfficeRepresentativeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\TrainingParticipantController;
use App\Http\Controllers\TrainingParticipantRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfMailController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\TwoFactorController;

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
    // return redirect('/login');
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/test', function () {

    // $ai = new OpenAIService();
    // $res = $ai->generateChatResponse("hello?");

    // var_dump($res);
    // return Inertia::render('Test');
    // return view('emails.test', [
    //     'name' => 'Dioame Jade C. Rendon',
    //     'training' => 'Community Volunteers Training on Project Implementation',
    //     'date' => 'January 2–3, 2025',
    //     'venue' => 'Almont Inland Resort, Butuan City',
    //     'givenDate' => '3rd day of January 2025',
    // ]);
});

Route::get('/test-send-pdf-email', [PdfMailController::class, 'send']);


Route::get('/thank-you-response', function(){ return view('thanks'); })->name('thanks.response');
Route::get('training/certificate/participation', [CertificateController::class, 'participation'])->name('public.cert.participant');
Route::get('training/certificate/generate', [CertificateController::class, 'generate'])->name('public.cert.generate');

Route::post('training/evaluation/public', [TrainingController::class, 'PublicEvaluationFormStore'])->name('public.training.evaluation.post');
Route::get('training/{id}/evaluation-response/public', [TrainingController::class, 'PublicEvaluationForm'])->name('public.training.evaluation-response');

Route::get('training/{id}/participants/registration', [TrainingParticipantRegistrationController::class, 'index'])->name('training.participants.registration.index');
Route::post('training/participants/register', [TrainingParticipantRegistrationController::class, 'register'])->name('training.participants.register');
Route::get('training/participants/register/sent', [TrainingParticipantRegistrationController::class, 'registrationSent'])->name('training.participants.registration.sent');
Route::get('training/{id}/participants/attendance', [TrainingParticipantController::class, 'attendance'])->name('training.participants.attendance.index');



Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->name('mfa.setup');
Route::post('/2fa/setup', [TwoFactorController::class, 'enable']);

Route::get('/mfa', [TwoFactorController::class, 'showPrompt'])->name('mfa.prompt');
Route::post('/mfa', [TwoFactorController::class, 'verify']);

Route::get('/cert/verification/{token}/{fullname}', [CertificateController::class, 'verify'])->name('cert.verify');

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard')->middleware('check.external');


    // CAN MANAGE TRAINING
    Route::group(['middleware' => ['role:staff-admin|staff']], function() {
        Route::post('training/evaluation', [TrainingController::class, 'StoreEvaluation'])->name('training.evaluation.post');
        Route::put('training-facilitators', [TrainingController::class, 'facilitateFacilitator'])->name('training.facilitators');
        Route::put('training-key-factors/{id}', [TrainingController::class, 'UpdateKeyFactors'])->name('training.key_factors');
        Route::put('training-resource-person', [TrainingController::class, 'UpdateResourcePerson'])->name('training.resource-person');
        Route::put('training-resource-person-rating', [TrainingController::class, 'SendRPRating'])->name('training.resource-person.rating');
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

        Route::get('training/certificate-request', [TrainingController::class, 'TrainingCertificateRequest'])->name('training.certificate-request');
        Route::put('training/update-certificate-request', [TrainingController::class, 'UpdateTrainingCertificateRequest'])->name('training.update-certificate-request');

        Route::apiResource('training-participant', TrainingParticipantController::class)->names('training.participant');
        Route::resource('training', TrainingController::class)->names('training');

        // Attachments
        Route::post('attachments-file', [AttachmentsController::class, 'storeUpdate'])->name('attachments.store-update');
        Route::delete('attachments-file/remove', [AttachmentsController::class, 'removeAttachment'])->name('attachments.desctroy');
        Route::resource('attachments', AttachmentsController::class)->names('attachments');
    
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

    // CAN USER MANAGE
    Route::group(['middleware' => ['role:staff-admin']], function() {

        //Settings
        Route::group(['prefix' => 'settings'], function(){
            Route::get('/', [SettingsController::class, 'Index'])->name('settings.index');
        });

         //User management
        Route::group(['prefix' => 'user-management'], function(){
            Route::get('/', [UserManagementController::class, 'Index'])->name('user-management');
            Route::post('/user-management/roles', [UserManagementController::class, 'userManagementRoles'])->name('user-management.roles');
        });
    });   


    Route::get('/openai/summarize', [OpenAIController::class, 'summarize'])->name('openai.summarize');
    
});

require __DIR__.'/auth.php';
require __DIR__.'/external-users.php';
