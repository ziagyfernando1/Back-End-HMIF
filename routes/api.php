<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AspirationController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PeriodController;
use App\Http\Controllers\API\DivisionController;
use App\Http\Controllers\API\Event_PartnershipController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ManagementController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\PartnershipController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\RecruitmentController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\SelectionController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\StructureController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VisimisiController;
use App\Http\Controllers\API\SubjectController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/get_article', [ArticleController::class, 'getArticles']);
Route::get('/get_article/{id}', [ArticleController::class, 'show']);
Route::get('/get_news', [ArticleController::class, 'getNews']);
Route::get('/get_news/{id}', [ArticleController::class, 'show']);
Route::get('/get_event', [EventController::class, 'index']);
Route::get('/get_event/{id}', [EventController::class, 'show']);
Route::get('/get_division', [DivisionController::class, 'index']);
Route::get('/get_division/{id}', [DivisionController::class, 'show']);
Route::get('/get_visimisi', [VisimisiController::class, 'index']);
Route::get('/get_visimisi/active', [VisimisiController::class, 'indexActive']);
Route::get('/get_visimisi/{id}', [VisimisiController::class, 'show']);

Route::post('/nim_check', [AuthController::class, 'nimChecking']);

Route::post('/aspiration/store', [AspirationController::class, 'store']);

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/memberauth', [MemberController::class, 'memberauth']);

    Route::get('/structure', [StructureController::class, 'index']);
    Route::prefix('/structure')->group(function () {
        Route::get('/detail/{id}', [StructureController::class, 'show']);
        Route::post('/store', [StructureController::class, 'store']);
        Route::put('/doUpdate/{id}', [StructureController::class, 'update']);
        Route::delete('/doDelete/{id}', [StructureController::class, 'destroy']);
    });

    Route::get('/period', [PeriodController::class, 'index']);
    Route::prefix('/period')->group(function () {
        Route::get('/detail/{id}', [PeriodController::class, 'show']);
        Route::post('/store', [PeriodController::class, 'store']);
        Route::put('/doUpdate/{id}', [PeriodController::class, 'update']);
        Route::delete('/doDelete/{id}', [PeriodController::class, 'destroy']);
    });

    Route::get('/member', [MemberController::class, 'index']);
    Route::prefix('/member')->group(function () {
        Route::get('/detail/{id}', [MemberController::class, 'show']);
        Route::post('/store', [MemberController::class, 'store']);
        Route::post('/doUpdate/{id}', [MemberController::class, 'update']);
        Route::delete('/doDelete/{id}', [MemberController::class, 'destroy']);
    });

    Route::get('/division', [DivisionController::class, 'index']);
    Route::prefix('/division')->group(function () {
        Route::get('/detail/{id}', [DivisionController::class, 'show']);
        Route::post('/store', [DivisionController::class, 'store']);
        Route::put('/doUpdate/{id}', [DivisionController::class, 'update']);
        Route::delete('/doDelete/{id}', [DivisionController::class, 'destroy']);
    });

    Route::get('/management', [ManagementController::class, 'index']);
    Route::prefix('/management')->group(function () {
        Route::get('/detail/{id}', [ManagementController::class, 'show']);
        Route::post('/store', [ManagementController::class, 'store']);
        Route::put('/doUpdate/{id}', [ManagementController::class, 'update']);
        Route::delete('/doDelete/{id}', [ManagementController::class, 'destroy']);
    });

    Route::get('/staff', [StaffController::class, 'index']);
    Route::prefix('/staff')->group(function () {
        Route::get('/detail/{id}', [StaffController::class, 'show']);
        Route::post('/store', [StaffController::class, 'store']);
        Route::put('/doUpdate/{id}', [StaffController::class, 'update']);
        Route::delete('/doDelete/{id}', [StaffController::class, 'destroy']);
    });

    Route::get('/program', [ProgramController::class, 'index']);
    Route::prefix('/program')->group(function () {
        Route::get('/detail/{id}', [ProgramController::class, 'show']);
        Route::post('/store', [ProgramController::class, 'store']);
        Route::put('/doUpdate/{id}', [ProgramController::class, 'update']);
        Route::delete('/doDelete/{id}', [ProgramController::class, 'destroy']);
    });


    Route::get('/category', [CategoryController::class, 'index']);
    Route::prefix('/category')->group(function () {
        Route::get('/detail/{id}', [CategoryController::class, 'show']);
        Route::post('/store', [CategoryController::class, 'store']);
        Route::put('/doUpdate/{id}', [CategoryController::class, 'update']);
        Route::delete('/doDelete/{id}', [CategoryController::class, 'destroy']);
    });

    Route::get('/partnership', [PartnershipController::class, 'index']);
    Route::prefix('/partnership')->group(function () {
        Route::get('/detail/{id}', [PartnershipController::class, 'show']);
        Route::post('/store', [PartnershipController::class, 'store']);
        Route::put('/doUpdate/{id}', [PartnershipController::class, 'update']);
        Route::delete('/doDelete/{id}', [PartnershipController::class, 'destroy']);
    });

    Route::get('/event', [EventController::class, 'index']);
    Route::prefix('/event')->group(function () {
        Route::post('/store', [EventController::class, 'store']);
        Route::put('/doUpdate/{id}', [EventController::class, 'update']);
        Route::delete('/doDelete/{id}', [EventController::class, 'destroy']);
        Route::get('/detail/{id}', [EventController::class, 'show']);
    });

    Route::get('/article', [ArticleController::class, 'index']);
    Route::prefix('/article')->group(function () {
        Route::post('/store', [ArticleController::class, 'store']);
        Route::get('/detail/{id}', [ArticleController::class, 'show']); 
        Route::post('/doUpdate/{id}', [ArticleController::class, 'update']);
        Route::delete('/doDelete/{id}', [ArticleController::class, 'destroy']);
    });

    Route::get('/event_partnership', [Event_PartnershipController::class, 'index']);
    Route::prefix('/event_partnership')->group(function () {
        Route::post('/store', [Event_PartnershipController::class, 'store']);
        Route::put('/doUpdate/{id}', [Event_PartnershipController::class, 'update']);
        Route::delete('/doDestroy/{id}', [Event_PartnershipController::class, 'destroy']);
        Route::get('/detail/{id}', [Event_PartnershipController::class, 'show']);
    });

    Route::get('/visi_misi', [VisiMisiController::class, 'index']);
    Route::prefix('/visi_misi')->group(function () {
        Route::post('/store', [VisiMisiController::class, 'store']);
        Route::put('/doUpdate/{id}', [VisiMisiController::class, 'update']);
        Route::delete('/doDestroy/{id}', [VisiMisiController::class, 'destroy']);
        Route::get('/detail/{id}', [VisiMisiController::class, 'show']);
    });
    
    Route::get('/subject', [SubjectController::class, 'index']);
    Route::prefix('/subject')->group(function () {
        Route::post('/store', [SubjectController::class, 'store']);
        Route::put('/doUpdate/{id}', [SubjectController::class, 'update']);
        Route::delete('/doDestroy/{id}', [SubjectController::class, 'destroy']);
        Route::get('/detail/{id}', [SubjectController::class, 'show']);
    });
    
    Route::get('/registration', [RegistrationController::class, 'index']);
    Route::prefix('/registration')->group(function () {
        Route::post('/store', [RegistrationController::class, 'store']);
        Route::put('/doUpdate/{id}', [RegistrationController::class, 'update']);
        Route::delete('/doDestroy/{id}', [RegistrationController::class, 'destroy']);
        Route::get('/detail/{id}', [RegistrationController::class, 'show']);
    });
    
    Route::get('/selection', [SelectionController::class, 'index']);
    Route::prefix('/selection')->group(function () {
        Route::post('/store', [SelectionController::class, 'store']);
        Route::put('/doUpdate/{id}', [SelectionController::class, 'update']);
        Route::delete('/doDestroy/{id}', [SelectionController::class, 'destroy']);
        Route::get('/detail/{id}', [SelectionController::class, 'show']);
    });
    
    Route::get('/recruitment', [RecruitmentController::class, 'index']);
    Route::prefix('/recruitment')->group(function () {
        Route::post('/store', [RecruitmentController::class, 'store']);
        Route::get('/detail/{id}', [RecruitmentController::class, 'show']);
        Route::put('/doUpdate/{id}', [RecruitmentController::class, 'update']);
        Route::delete('/doDelete/{id}', [RecruitmentController::class, 'destroy']);
    });

    Route::get('/aspiration', [AspirationController::class, 'index']);
    Route::prefix('/aspiration')->group(function () {
        Route::get('/detail/{id}', [AspirationController::class, 'show']);
        Route::delete('/doDelete/{id}', [AspirationController::class, 'destroy']);
    });

});
