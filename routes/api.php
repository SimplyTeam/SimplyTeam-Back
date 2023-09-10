<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\RewardApiController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ValidatePaymentApiController;
use App\Http\Controllers\WorkspaceApiController;
use App\Http\Controllers\WorkspaceInvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validatePayment', [ValidatePaymentApiController::class, 'index']);

    Route::get('/info', [InfoController::class, 'index']);

    Route::prefix('/quests')->group(function () {
        Route::get('/', [QuestController::class, 'index']);
    });

    Route::prefix('workspaces')->group(function () {
        Route::get('/', [WorkspaceApiController::class, 'index']);
        Route::get('/rewards', [RewardApiController::class, 'index']);
        Route::get('/{workspace}', [WorkspaceApiController::class, 'show']);
        Route::delete('/{workspace}/users/{user}', [WorkspaceApiController::class, 'removeUser']);
        Route::post('/', [WorkspaceApiController::class, 'store']);
        Route::put('/{workspace}', [WorkspaceApiController::class, 'update']);
        Route::delete('/{workspace}', [WorkspaceApiController::class, 'destroy']);
        Route::post('/invitations/accept', [WorkspaceInvitationController::class, 'accept']);
        Route::prefix("/{workspace}/projects")->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::prefix('/{project}')->group(function () {
                Route::prefix('/tasks')->group(function () {
                    Route::get('/', [TaskController::class, 'index']);
                    Route::get('/backlog', [TaskController::class, 'backlog']);
                    Route::post('/', [TaskController::class, 'store']);
                    Route::put('/{task}', [TaskController::class, 'update']);
                    Route::delete('/{task}', [TaskController::class, 'remove']);
                });
            });
            Route::prefix('/{project}/sprints')->group(function () {
                Route::get('/', [SprintController::class, 'index']);
                Route::post('/', [SprintController::class, 'store']);
                Route::prefix('/{sprint}')->group(function () {
                    Route::put('/', [SprintController::class, 'update']);
                    Route::delete('/', [SprintController::class, 'remove']);
                });
            });
            Route::put('/{project}', [ProjectController::class, 'update']);

        });
    });
});
