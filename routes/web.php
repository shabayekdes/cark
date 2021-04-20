<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UploadController;
use App\Http\Controllers\Web\ProductController;

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

Auth::routes([
    'register' => false
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test', TestController::class);



Route::middleware('auth')->group(function () {
    Route::get('products/upload', [UploadController::class, 'create'])->name('products.create');
    Route::post('products/upload', [UploadController::class, 'store'])->name('products.store');

    Route::get('products/edit', [UploadController::class, 'edit'])->name('products.edit');
    Route::put('products/edit', [UploadController::class, 'update'])->name('products.update');

    Route::get('done', [UploadController::class, 'done'])->name('done');

    Route::get('products/edit-title', [ProductController::class, 'edit'])->name('products.edit.title');
    Route::put('products/edit-title', [ProductController::class, 'update'])->name('products.update.title');


});
