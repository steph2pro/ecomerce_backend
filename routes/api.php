<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\UserController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommandeController;




Route::apiResource('users', UserController::class);
Route::get('users/{id}/articles', [UserController::class, 'getArticles']);
Route::get('users/{id}/commandes', [UserController::class, 'getCommandes']);
// Route::post('users', [UserController::class, 'store']);
Route::put('users/{id}', [UserController::class, 'update']);

Route::post('/users', [UserController::class, 'store']);

//categories
Route::apiResource('categories', CategoryController::class);
Route::get('categories/{id}/articles', [CategoryController::class, 'getArticles']);


//articles
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);
Route::post('articles', [ArticleController::class, 'store']);
Route::put('articles/{id}', [ArticleController::class, 'update']);
Route::delete('articles/{id}', [ArticleController::class, 'destroy']);


//commande

Route::get('commandes', [CommandeController::class, 'index']);
Route::get('commandes/{id}', [CommandeController::class, 'show']);
Route::post('commandes', [CommandeController::class, 'store']);
Route::put('commandes/{id}', [CommandeController::class, 'update']);
Route::delete('commandes/{id}', [CommandeController::class, 'destroy']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');
