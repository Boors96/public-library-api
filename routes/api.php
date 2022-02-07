<?php

use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// get books in api
Route::get('books', [BookController::class, 'getAllBooks']);

// add books to api
Route::post('add-book', [BookController::class, 'addBook']);

// like a book
Route::put('like', [BookController::class, 'like']);

// comment on a book
Route::post('comment', [BookController::class, 'comment']);
