<?php

use App\Http\Controllers\App;
use App\Http\Controllers\JasaPelayananController;
use App\Http\Controllers\JasaSaranaController;
use App\Http\Controllers\JenisJasaAkunController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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
    Route::delete('/service-types/{id}', [JenisJasaAkunController::class, 'destroy'])->name('service-types.destroy');
    Route::get('/service-types/{id}/edit', [JenisJasaAkunController::class, 'edit'])->name('service-types.edit');
    Route::post('/service-types', [JenisJasaAkunController::class, 'store'])->name('service-types.store');
    Route::get('/service-types', [JenisJasaAkunController::class, 'get'])->name('service-types.get');
    Route::post('/use-template', [JenisJasaAkunController::class, 'useTemplate'])->name('use-template.get');
    Route::get('/dashboard', [App::class, 'index'])->name('dashboard');
    Route::get('/js', [JasaSaranaController::class, 'index'])->name('js');
    Route::get('/jp', [JasaPelayananController::class, 'index'])->name('jp');
    Route::post('/submit-percentage', [JasaPelayananController::class, 'submitPercentage'])->name('submit-percentage');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/upload-shifting', [App::class, 'uploadShifting'])->name('uploadShifting');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/set-service-type', [JenisJasaAkunController::class, 'index'])->name('set-service-type');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
