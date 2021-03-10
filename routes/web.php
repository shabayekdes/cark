<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UploadController;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test', TestController::class);



Route::middleware('auth')->group(function () {
    Route::get('products/upload', [UploadController::class, 'create'])->name('products.create');
    Route::post('products/upload', [UploadController::class, 'store'])->name('products.store');

    Route::get('products/edit', [UploadController::class, 'edit'])->name('products.edit');
    Route::put('products/edit', [UploadController::class, 'update'])->name('products.update');
});

