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
use App\Http\Controllers\Auth\AuthenticationController;

//Auth::routes(['register' => false]);

Route::get('/login/{social}', [AuthenticationController::class, 'getSocialRedirect']);
Route::get('/login/{social}/callback', [AuthenticationController::class, 'getSocialCallback']);

/*
Route::get('/mailable', function () {
    $season = App\Models\Season::find(9);
    //Mail::to('tomfa@hotmail.com')->send(new App\Mail\NextPlayDate($season));
    //->cc($moreUsers)
    //->bcc($evenMoreUsers)
    return new App\Mail\NextPlayDate($season);
});
*/

//vue router will handle all routing
Route::get('/{any}', function () {
    return view('home');
})->where('any', '.*');
