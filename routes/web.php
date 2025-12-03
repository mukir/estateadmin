<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BusinessOnboardingController;
use App\Http\Controllers\BusinessAppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstateController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ServiceChargeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::post('/start-trial', [BusinessOnboardingController::class, 'store'])->name('onboarding.start');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user?->business) {
            return redirect()->route('business.dashboard', ['business' => $user->business->slug]);
        }

        return redirect()->route('landing');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mfa/setup', [\App\Http\Controllers\MfaController::class, 'showSetup'])->name('mfa.setup');
    Route::post('/mfa/enable', [\App\Http\Controllers\MfaController::class, 'enable'])->name('mfa.enable');
    Route::get('/mfa/challenge', [\App\Http\Controllers\MfaController::class, 'challenge'])->name('mfa.challenge');
    Route::post('/mfa/verify', [\App\Http\Controllers\MfaController::class, 'verify'])->name('mfa.verify');
    Route::post('/mfa/disable', [\App\Http\Controllers\MfaController::class, 'disable'])->name('mfa.disable');
});

Route::prefix('/b/{business:slug}')
    ->middleware(['business', 'auth', 'business.access', 'mfa', 'role:admin,finance,collections,viewer'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('business.dashboard');

        Route::post('/houses/import', [HouseController::class, 'import']);
        Route::prefix('imports')->group(function () {
            Route::get('/template/{type}', [ImportController::class, 'template'])->name('app.import.template');
            Route::post('/estates', [ImportController::class, 'importEstates'])->name('app.import.estates');
            Route::post('/houses', [ImportController::class, 'importHouses'])->name('app.import.houses');
            Route::post('/residents', [ImportController::class, 'importResidents'])->name('app.import.residents');
        });

        Route::apiResource('estates', EstateController::class);
        Route::apiResource('houses', HouseController::class);
        Route::apiResource('residents', ResidentController::class);
        Route::apiResource('service-charges', ServiceChargeController::class);
        Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show', 'store', 'update']);
        Route::apiResource('payments', PaymentController::class)->only(['index', 'store']);

        Route::get('reports/arrears', [ReportController::class, 'arrears']);
        Route::get('reports/collections', [ReportController::class, 'collections']);
        Route::get('reports/occupancy', [ReportController::class, 'occupancy']);
        Route::get('reports/residents/{resident}/statement', [ReportController::class, 'residentStatement']);

        Route::prefix('app')->group(function () {
            Route::get('/estates', [BusinessAppController::class, 'estates'])->name('app.estates');
            Route::post('/estates', [BusinessAppController::class, 'storeEstate'])->name('app.estates.store');

            Route::get('/houses', [BusinessAppController::class, 'houses'])->name('app.houses');
            Route::post('/houses', [BusinessAppController::class, 'storeHouse'])->name('app.houses.store');

            Route::get('/residents', [BusinessAppController::class, 'residents'])->name('app.residents');
            Route::post('/residents', [BusinessAppController::class, 'storeResident'])->name('app.residents.store');
            Route::get('/residents/{resident}/statement', [BusinessAppController::class, 'residentStatement'])->name('app.residents.statement');

            Route::get('/service-charges', [BusinessAppController::class, 'serviceCharges'])->name('app.service-charges');
            Route::post('/service-charges', [BusinessAppController::class, 'storeServiceCharge'])->name('app.service-charges.store');

            Route::get('/invoices', [BusinessAppController::class, 'invoices'])->name('app.invoices');
            Route::post('/invoices', [BusinessAppController::class, 'storeInvoice'])->name('app.invoices.store');
            Route::post('/invoices/run-recurring', [BusinessAppController::class, 'runRecurringInvoices'])->name('app.invoices.run');
            Route::get('/invoices/{invoice}/pdf', [BusinessAppController::class, 'invoicePdf'])->name('app.invoices.pdf');

            Route::get('/payments', [BusinessAppController::class, 'payments'])->name('app.payments');
            Route::post('/payments', [BusinessAppController::class, 'storePayment'])->name('app.payments.store');

            Route::get('/reports', [BusinessAppController::class, 'reports'])->name('app.reports');
            Route::get('/reports/export/{type}', [ReportController::class, 'exportCsv'])->name('app.reports.export');
            Route::post('/follow-ups', [BusinessAppController::class, 'storeFollowUp'])->name('app.followups.store');
        });
    });

require __DIR__.'/auth.php';
