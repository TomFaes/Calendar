<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticationController;

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index']);

//Auth::routes(['register' => false]);

Route::get('/logout', [AuthenticationController::class, 'logout']);
Route::get('/login/{social}', [AuthenticationController::class, 'getSocialRedirect'])->middleware('guest');;
Route::get('/login/{social}/callback', [AuthenticationController::class, 'getSocialCallback'])->middleware('guest');;
Route::post('login/standard', [AuthenticationController::class, 'login']);


//vue router will handle all routing

Route::get('/{any}', function () {
    return view('home');
})->where('any', '.*');
