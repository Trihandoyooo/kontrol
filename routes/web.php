<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('welcome');
});

Route::get('/inputrapat', function () {
    return view('admin.inputrapat');
});

Route::get('/viewrapat', function () {
    return view('admin.viewrapat');
});

Route::get('/inputiuran', function () {
    return view('iuran.inputiuran');
});

Route::get('/viewiuran', function () {
    return view('iuran.viewiuran');
});

Route::get('/viewtransaksi', function () {
    return view('iuran.viewtransaksi');
});

Route::get('/inputkaderisasi', function () {
    return view('kaderisasi.inputkaderisasi');
});

Route::get('/viewkaderisasi', function () {
    return view('kaderisasi.viewkaderisasi');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
