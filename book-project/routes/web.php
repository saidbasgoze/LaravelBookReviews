<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource("books",BookController::class)
    ->only(["index","show"]);

Route::resource("books.reviews",ReviewController::class)
        ->scoped(["review" => "book"])
        ->only(["create","store"]);