<?php

use Illuminate\Support\Facades\Route;

// homepage
Route::livewire('/', 'pages::main');
// daftar produk
Route::livewire('/{type}/{provider}', 'pages::product-list');

// login
Route::livewire('/auth', 'pages::login')->name('login')->middleware('guest');
Route::middleware('auth')->group(function () {
    // tambah produk
    Route::livewire('/new', 'pages::product-new');
    // edit produk
    Route::livewire('/edit', 'pages::product-edit');
});

// Route::get('/', function () {
//     return view('welcome');
// });
