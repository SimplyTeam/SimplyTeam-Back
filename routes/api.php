<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\WorkspaceController;
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

    Route::prefix('workspaces')->group(function () {
        Route::get('/', [WorkspaceController::class, 'index']);
        Route::get('/{workspace}', [WorkspaceController::class, 'show']);
        Route::delete('/{workspace}/users/{user}', [WorkspaceController::class, 'removeUser']);
        Route::post('/', [WorkspaceController::class, 'store']);
        Route::put('/{workspace}', [WorkspaceController::class, 'update']);
        Route::delete('/{workspace}', [WorkspaceController::class, 'destroy']);
        Route::post('/invitations/accept', [WorkspaceInvitationController::class, 'accept']);
        Route::prefix("/{workspace}/projects")->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::prefix('/{project}/sprints')->group(function () {
                Route::get('/', [SprintController::class, 'index']);
                Route::post('/', [SprintController::class, 'store']);
                Route::put('/{sprint}', [SprintController::class, 'update']);
            });
            Route::put('/{project}', [ProjectController::class, 'update']);
        });
    });
});
