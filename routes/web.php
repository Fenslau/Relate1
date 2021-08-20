<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/stat', function () {
    return view('stat');
})->name('stat');

Route::get('/tarifs', function () {
    return view('tarifs');
})->name('tarifs');

Route::get('/groupsearch', function () {
    return view('groupsearch');
})->name('groupsearch');

Route::get('/auditoria', function () {
    return view('auditoria');
})->name('auditoria');

Route::get('/getusers', function () {
    return view('getusers');
})->name('getusers');

Route::get('/new-users', function () {
    return view('new-users');
})->name('new-users');

Route::get('/stream', function () {
    return view('stream');
})->name('stream');

Route::post('/qiwi_request', 'App\Http\Controllers\QiwiRequestController@confirm')
->name('qiwi-request');

Route::post('/tarifs/choose', 'App\Http\Controllers\TarifController@choose')
->name('tarif-choose');

Route::get('/login', 'App\Http\Controllers\AuthController@authVK')
->name('auth-vk');

Route::get('/vk-auth-code', 'App\Http\Controllers\AuthController@authVKcode')
->name('auth-vk-code');

Route::get('/logout', 'App\Http\Controllers\AuthController@authVKdestroy')
->name('auth-vk-destroy');

Route::post('/opros', 'App\Http\Controllers\FeedbackController@send')->name('opros');
