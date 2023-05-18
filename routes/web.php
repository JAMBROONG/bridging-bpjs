<?php

use App\Http\Controllers\App;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/shifting', function () {
    return Inertia::render('Shifting',[
        'response' => "Hallo"
    ]);
})->middleware(['auth', 'verified'])->name('shifting');


Route::get('/distribution', function () {
    return Inertia::render('Distribution');
})->middleware(['auth', 'verified'])->name('distribution');

Route::get('/application', function () {
    return Inertia::render('Application');
})->middleware(['auth', 'verified'])->name('application');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/upload-shifting', [App::class, 'uploadShifting'])->name('uploadShifting');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
