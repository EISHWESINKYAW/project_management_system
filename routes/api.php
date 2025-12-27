<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('broadcasting/auth', function (Request $request) {
        return Broadcast::auth($request);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('change-password/{id}', [AuthController::class, 'changePassword']);

    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Welcome to dashboard']);
    });

    Route::get('/permissions', [RoleController::class, 'getAllPermissions']);

    Route::group(['prefix' => 'roles'], function () {
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/', [RoleController::class, 'list']);
        Route::get('/permissions', [RoleController::class, 'getRolePermissions']);
        Route::get('/{id}', [RoleController::class, 'detail']);
        Route::post('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'delete']);
    });

    Route::group(['prefix' => 'user-role'], function () {
        Route::get('/list', [UserRolePermission::class, 'list']);
        Route::post('/permission', [UserRolePermission::class, 'store']);
        Route::get('/permission/{id}', [UserRolePermission::class, 'detail']);
        Route::post('/permission/{id}', [UserRolePermission::class, 'update']);
    });

    //project routes
    Route::prefix('project')->group(function () {
        Route::get('/list', [ProjectController::class, 'list']);
        Route::get('/colaborator/list', [ProjectController::class, 'collaborator']);
        Route::get('/colaborator/list/{id}', [ProjectController::class, 'projectColaborator']);
        Route::get('/list-for-task-store', [ProjectController::class, 'listForTaskStore']);
        Route::post('/store', [ProjectController::class, 'store']);
        Route::get('/detail/{id}', [ProjectController::class, 'detail']);
        Route::get('/detail-with-tasks/{id}', [ProjectController::class, 'detailWithTasks']);
        Route::post('/update/{id}', [ProjectController::class, 'update']);
        Route::post('/make-complete/{id}', [ProjectController::class, 'makeComplete']);
        Route::delete('/delete/{id}', [ProjectController::class, 'delete']);
    });

    //Task routes
    Route::prefix('task')->group(function () {
        Route::get('/list', [TaskController::class, 'list']);
        Route::post('/store', [TaskController::class, 'store']);
        Route::get('/detail/{id}', [TaskController::class, 'detail']);
        Route::post('/update/{id}', [TaskController::class, 'update']);
        Route::post('/update-status/{id}', [TaskController::class, 'updateStatus']);
        Route::delete('/delete/{id}', [TaskController::class, 'delete']);
    });

    // user routes
    Route::prefix('users')->group(function () {
        Route::get('/list', [UserController::class, 'list']);
        Route::post('/store', [UserController::class, 'store']);
        Route::get('/detail/{id}', [UserController::class, 'detail']);
        Route::post('/update/{id}', [UserController::class, 'update']);
        Route::post('/profile/{id}', [UserController::class, 'updateProfile']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/list', [NotificationController::class, 'index']);
        Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);
        Route::delete('/delete/{id}', [NotificationController::class, 'delete']);
    });

    //Comment routes
    Route::prefix('comment')->group(function () {
        Route::post('/commentable/{id}', [CommentController::class, 'storeComment']);
        Route::get('/commentable/{id}', [CommentController::class, 'getComments']);
    });

    //Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/recent-projects', [DashboardController::class, 'latestProjects']);
        Route::get('/recent-tasks', [DashboardController::class, 'latestTasks']);
    });
});
