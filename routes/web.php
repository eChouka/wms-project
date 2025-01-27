<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\CustomizerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ExternalController;

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
    return view('welcome');
});

// activate user
Route::get('/activate/user/{token}', [ExternalController::class, 'activate_user']);
Route::post('/activate/user/', [ExternalController::class, 'activate_user_save']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/model/{model}/bulk-import', [AutoController::class, 'bulk_import']);
Route::post('/model/{model}/bulk-import', [AutoController::class, 'bulk_import_save']);

Route::get('/model/{model}/create', [AutoController::class, 'create']);
Route::post('/model/{model}', [AutoController::class, 'store']);
Route::get('/model/{model}/{id}/edit', [AutoController::class, 'edit']);
Route::put('/model/{model}/{id}', [AutoController::class, 'update']);
Route::delete('/model/{model}/{id}', [AutoController::class, 'destroy']);
// index
Route::get('/model/{model}', [AutoController::class, 'index']);
//shows
Route::get('/model/{model}/{id}', [AutoController::class, 'show']);

// show page
Route::get('/model/{model}/{id}/{page}', [AutoController::class, 'show_page']);

// quick update
Route::post('/model/{model}/quick-update', [AutoController::class, 'quick_update']);

//customizer
Route::get('customizer', [CustomizerController::class, 'index'])->name('customizer.index');
Route::get('/customizer/create', [CustomizerController::class, 'create'])->name('customizer.create');
Route::post('/customizer/new', [CustomizerController::class, 'store'])->name('customizer.store');
Route::get('/customizer/edit', [CustomizerController::class, 'edit'])->name('customizer.edit');
Route::post('/customizer/edit', [CustomizerController::class, 'update'])->name('customizer.update');

Route::get('/customizer/edit-fields', [CustomizerController::class, 'edit_fields'])->name('customizer.edit.fields');
Route::post('/customizer/edit-fields', [CustomizerController::class, 'update_fields'])->name('customizer.update.fields');

Route::get('/customizer/edit-relations', [CustomizerController::class, 'edit_relations'])->name('customizer.edit.relations');
Route::post('/customizer/edit-relations', [CustomizerController::class, 'update_relations'])->name('customizer.update.relations');

Route::get('/customizer/edit-events', [CustomizerController::class, 'edit_events'])->name('customizer.edit.events');
Route::post('/customizer/edit-events', [CustomizerController::class, 'update_events'])->name('customizer.update.events');

Route::get('/customizer/edit-actions', [CustomizerController::class, 'edit_actions'])->name('customizer.edit.actions');
Route::post('/customizer/edit-actions', [CustomizerController::class, 'update_actions'])->name('customizer.update.actions');

Route::get('/customizer/edit-pages', [CustomizerController::class, 'edit_pages'])->name('customizer.edit.pages');
Route::post('/customizer/edit-pages', [CustomizerController::class, 'update_pages'])->name('customizer.update.pages');

Route::get('account', [AccountController::class, 'index'])->name('account.index');
Route::put('account', [AccountController::class, 'store'])->name('account.store');
