<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web_tamu_Controlllers;

Route::resource('/web_tamu', web_tamu_Controlllers::class);

Route::get('/', function () {
    return view('welcome');
});
