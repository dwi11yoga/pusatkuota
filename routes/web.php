<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::main');
Route::livewire('/{type}/{provider}', 'pages::product-list');
Route::livewire('/new', 'pages::product-new');

// Route::get('/', function () {
//     return view('welcome');
// });
