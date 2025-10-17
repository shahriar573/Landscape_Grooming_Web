<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorCollaborationController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\CollabSessionController;
use App\Http\Controllers\Api\VendorMutationController;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('/users/check-mobile', [UserController::class, 'checkMobile']);

    Route::get('vendors', [VendorController::class, 'index']);
    Route::get('vendors/{vendor}', [VendorController::class, 'show']);
    
    Route::middleware('auth')->group(function () {
        // Vendor CRUD
        Route::post('vendors', [VendorController::class, 'store']);
        Route::patch('vendors/{vendor}', [VendorController::class, 'update']);
        Route::delete('vendors/{vendor}', [VendorController::class, 'destroy']);
        
        // Vendor Collaboration Bootstrap
        Route::get('vendors/{vendor}/collaboration/bootstrap', VendorCollaborationController::class);
        
        // Collaboration Sessions
        Route::post('vendors/{vendor}/session/start', [CollabSessionController::class, 'start']);
        Route::post('vendors/{vendor}/session/{session}/end', [CollabSessionController::class, 'end']);
        Route::post('vendors/{vendor}/session/{session}/heartbeat', [CollabSessionController::class, 'heartbeat']);
        Route::get('vendors/{vendor}/session/{session}/participants', [CollabSessionController::class, 'participants']);
        Route::get('vendors/{vendor}/session/{session}/events', [CollabSessionController::class, 'events']);
        
        // Vendor Mutations with Versioning
        Route::post('vendors/{vendor}/mutate', [VendorMutationController::class, 'apply']);
        Route::get('vendors/{vendor}/state', [VendorMutationController::class, 'getState']);
        Route::get('vendors/{vendor}/revisions', [VendorMutationController::class, 'revisions']);
        Route::post('vendors/{vendor}/revisions/{revision}/rollback', [VendorMutationController::class, 'rollback']);
    });
});
