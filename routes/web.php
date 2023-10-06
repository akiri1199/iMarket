<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\SoureController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [UserController::class, 'index'])->name('users.index'); // view
// Route::post('import', [UserController::class, 'import'])->name('users.import'); // import route
// Route::get('export', [UserController::class, 'export'])->name('users.export'); // export route


Route::get('/', [SoureController::class, 'index'])->name('source.index'); // view
Route::post('import', [SoureController::class, 'import'])->name('source.import'); // import route
Route::get('/load', [SoureController::class, 'load'])->name('source.load'); // view
Route::get('/search', [SoureController::class, 'search'])->name('source.search'); // view