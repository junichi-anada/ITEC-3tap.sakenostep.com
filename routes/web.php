<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('mymenu', function () {
    return view('user.mymenu');
});
