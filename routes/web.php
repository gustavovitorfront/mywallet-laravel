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


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/add-transaction', [App\Http\Controllers\TransactionController::class, 'form_add'])->middleware('auth')->name('add_transaction');
Route::post('/add-transaction', [App\Http\Controllers\TransactionController::class, 'form_add_submit'])->middleware('auth')->name('form_add_submit');

Route::get('/edit-transaction/{id}', [App\Http\Controllers\TransactionController::class, 'form_edit'])->middleware('auth')->name('edit_transaction');
Route::put('/edit-transaction/{id}', [App\Http\Controllers\TransactionController::class, 'form_edit_submit'])->middleware('auth')->name('form_edit_submit');
Route::get('/delete-transaction/{id}', [App\Http\Controllers\TransactionController::class, 'delete'])->middleware('auth')->name('delete_transaction');
