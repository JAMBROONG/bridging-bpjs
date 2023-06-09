<?php

use App\Http\Controllers\App;
use App\Http\Controllers\DataDokterController;
use App\Http\Controllers\JasaPelayananController;
use App\Http\Controllers\JasaSaranaController;
use App\Http\Controllers\JenisJasaAkunController;
use App\Http\Controllers\KategoriPendapatanController;
use App\Http\Controllers\KpiDokterController;
use App\Http\Controllers\PpnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorObatController;
use App\Models\PercentageJsJp;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
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
    return Inertia::render('Welcome', [
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


Route::get('/allocation-distribution', function () {
    return Inertia::render('Distribution',[
        'file' => [
            'pathPendapatanRI' => Session::get('pathPendapatanRI'),
            'pathPendapatanRJ' => Session::get('pathPendapatanRJ'),
            'pathBPJSRI' => Session::get('pathBPJSRI'),
            'pathBPJSRJ' => Session::get('pathBPJSRJ'),
            'persentaseJSJP' => (empty(PercentageJsJp::where('user_id', Auth::id())->first()['jp'])) ? null : PercentageJsJp::where('user_id', Auth::id())->first()['jp']
        ]
    ]);
})->middleware(['auth', 'verified'])->name('allocation.distribution');


Route::get('/application', function () {
    return Inertia::render('Application');
})->middleware(['auth', 'verified'])->name('application');

Route::middleware('auth')->group(function () {
    //file
    Route::get('/file-clear', [App::class, 'clear'])->name('file.clear');

    // service
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

    //kategori pendapatan
    Route::get('/getData', [KategoriPendapatanController::class, 'show'])->name('kategoriPendapatan.get');
    
    // KPI
    Route::get('/kpi', [KpiDokterController::class, 'index'])->name('kpi');
    


    Route::post('/submit-percentage-jl', [JasaPelayananController::class, 'submitPercentageJlJtl'])->name('submit-percentage-jl');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // shifting
    Route::post('/upload-shifting', [App::class, 'uploadShifting'])->name('uploadShifting');
    Route::get('/data-shifting', [App::class, 'dataShifting'])->name('data-shifting');
    Route::get('/shifting-js', [App::class, 'shiftingJS'])->name('shifting.js');
    Route::get('/shifting-all', [App::class, 'shiftingAll'])->name('shifting.all');
    Route::get('/distribution-all', [App::class, 'distributionAll'])->name('distribution.all');
    Route::get('/shifting-jp', [App::class, 'shiftingJP'])->name('shifting.jp');

    //PPN
    
    Route::get('/ppn', [PpnController::class, 'index'])->name('ppn');
    Route::post('/upload-ppn', [PpnController::class, 'uploadPpn'])->name('upload-ppn');
    Route::get('/delPathFilePpn', [PpnController::class, 'delPathFilePpn'])->name('delete-ppn');

    // allocation and distribution
    Route::post('/upload-distribution', [App::class, 'uploadDistribution'])->name('uploadDistribution');
    Route::get('/data-distribution', [App::class, 'dataDistribution'])->name('data-distribution');

    Route::get('/piutang-bpjs-tak-tertagih', [App::class, 'piutangTakTertagih'])->name('piutang-bpjs-tak-tertagih');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/set-service-type', [JenisJasaAkunController::class, 'index'])->name('set-service-type');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // vendor
    Route::get('/vendorObat', [VendorObatController::class, 'index'])->name('vendorObat');
    Route::post('/vendor-add', [VendorObatController::class, 'addVendor'])->name('vendor.add');
    Route::post('/vendor-update', [VendorObatController::class, 'updateVendor'])->name('vendor.update');
    Route::post('/vendor-delete', [VendorObatController::class, 'deleteVendor'])->name('vendor.delete');

});

require __DIR__.'/auth.php';
