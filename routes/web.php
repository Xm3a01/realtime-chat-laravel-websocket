<?php

use App\Events\LisnerEvent;
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
    broadcast(new LisnerEvent('data'));
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['middleware' => 'auth'] , function(){
    
    Route::get('/chats', [App\Http\Controllers\ChatController::class, 'index'])->name('chats');
    Route::get('/get-message', [App\Http\Controllers\ChatController::class, 'getMessage'])->name('get.message');
    Route::post('/send-message', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send.message');
});

