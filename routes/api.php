<?php

use App\Http\Controllers\Api\{AuthController,BusinessController,BusinessLinkController,BusinessNoteController,BusinessSocialLinkController,DailyTaskController,DashboardController,DevelopmentKeyController,GoalController,JobApplicationController,LearningController,MyLinkController,ProfileController,SearchController,TaskController,WebsiteCheckController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status'=>'ok','service'=>'nexarch-api']));
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class,'login'])->middleware('throttle:10,1');
        Route::post('/register', [AuthController::class,'register'])->middleware('throttle:5,1');
        Route::post('/refresh', [AuthController::class,'refresh'])->middleware('throttle:30,1');
        Route::post('/forgot-password', [AuthController::class,'forgotPassword'])->middleware('throttle:5,1');
    });

    Route::middleware(['supabase.auth','throttle:api'])->group(function () {
        Route::get('/auth/me',[AuthController::class,'me']); Route::post('/auth/logout',[AuthController::class,'logout']); Route::put('/auth/password',[AuthController::class,'updatePassword']);
        Route::get('/profile',[ProfileController::class,'show']); Route::put('/profile',[ProfileController::class,'update']);
        Route::get('/dashboard',DashboardController::class); Route::get('/search',SearchController::class);
        Route::apiResources(['links'=>MyLinkController::class,'businesses'=>BusinessController::class,'learning'=>LearningController::class,'goals'=>GoalController::class,'daily-tasks'=>DailyTaskController::class,'tasks'=>TaskController::class,'job-applications'=>JobApplicationController::class]);
        Route::post('/businesses/{business}/links',[BusinessLinkController::class,'store']); Route::put('/businesses/{business}/links/{link}',[BusinessLinkController::class,'update']); Route::delete('/businesses/{business}/links/{link}',[BusinessLinkController::class,'destroy']);
        Route::post('/businesses/{business}/social-links',[BusinessSocialLinkController::class,'store']); Route::put('/businesses/{business}/social-links/{social}',[BusinessSocialLinkController::class,'update']); Route::delete('/businesses/{business}/social-links/{social}',[BusinessSocialLinkController::class,'destroy']);
        Route::put('/businesses/{business}/note',[BusinessNoteController::class,'upsert']); Route::post('/businesses/{business}/website-checks',WebsiteCheckController::class)->middleware('throttle:10,1');
        Route::post('/businesses/{business}/development-keys',[DevelopmentKeyController::class,'store']); Route::put('/businesses/{business}/development-keys/{key}',[DevelopmentKeyController::class,'update']); Route::delete('/businesses/{business}/development-keys/{key}',[DevelopmentKeyController::class,'destroy']);
    });
});
