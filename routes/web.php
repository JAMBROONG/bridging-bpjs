<?php

use App\Http\Controllers\App;
use App\Http\Controllers\DataDokterController;
use App\Http\Controllers\JasaPelayananController;
use App\Http\Controllers\JasaSaranaController;
use App\Http\Controllers\JenisJasaAkunController;
use App\Http\Controllers\KpiDokterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
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
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/shifting', function () {
    return Inertia::render('Shifting',[
        'file' => [
            'pathPendapatanRI' => Session::get('pathPendapatanRI'),
            'pathPendapatanRJ' => Session::get('pathPendapatanRJ'),
            'pathBPJSRI' => Session::get('pathBPJSRI'),
            'pathBPJSRJ' => Session::get('pathBPJSRJ')
        ]
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
    
    // data Dokter

    Route::get('/dokter', [DataDokterController::class, 'index'])->name('dokter.index');
    Route::post('/add-dokter', [DataDokterController::class, 'store'])->name('dokter.store');
    Route::delete('/delete-dokter/{id}', [DataDokterController::class, 'destroy'])->name('dokter.delete');
    
    // KPI
    Route::get('/kpi', [KpiDokterController::class, 'index'])->name('kpi');
    


    Route::post('/submit-percentage-jl', [JasaPelayananController::class, 'submitPercentageJlJtl'])->name('submit-percentage-jl');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/upload-shifting', [App::class, 'uploadShifting'])->name('uploadShifting');
    Route::get('/data-shifting', [App::class, 'dataShifting'])->name('data-shifting');
    Route::get('/piutang-bpjs-tak-tertagih', [App::class, 'piutangTakTertagih'])->name('piutang-bpjs-tak-tertagih');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/set-service-type', [JenisJasaAkunController::class, 'index'])->name('set-service-type');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
