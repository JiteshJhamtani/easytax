<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Agent\ApplicationController as AgentApplicationController;
use App\Http\Controllers\Agent\CommissionController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Front\ApplicationController;
use App\Http\Controllers\Front\ServiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Deprecated KPI and Switch Server routes moved/removed
/*
|--------------------------------------------------------------------------
| Public

|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('services.index');
})->name('home');

// --- NEW PUBLIC TRACKING ROUTE ---
Route::get('/track/{application}', [\App\Http\Controllers\Front\ApplicationController::class, 'track'])
    ->name('tracking.show')
    ->middleware('signed'); // This is the magic lock!
// ---------------------------------

Route::get('/pages/{slug}', [\App\Http\Controllers\Front\PageController::class, 'show'])
    ->name('pages.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');
});

// The page that shows the "Forgot Password" form
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

// The route that actually sends the email
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// The link the user clicks inside their email
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

// The route that processes the new password submission
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');
/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/
Route::get('/auto-login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'crossServerLogin']);

Route::post('/agent/validate-coupon', [\App\Http\Controllers\Agent\CouponController::class, 'validateCoupon'])->name('agent.validate_coupon');

Route::middleware(['auth', 'agent', 'sidebar'])->prefix('services')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])
        ->name('services.index');

    Route::get('/{slug}', [ServiceController::class, 'show'])
        ->name('services.show');

    Route::post('/{slug}/apply', [ApplicationController::class, 'store'])
        ->name('applications.store');

});

Route::middleware(['auth', 'agent', 'sidebar'])->prefix('agent')->name('agent.')->group(function () {

    Route::get('/dashboard', [AgentDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/gifts', [AgentDashboardController::class, 'gifts'])
        ->name('gifts'); // view all gifts

    Route::get('/applications', [AgentApplicationController::class, 'index'])
        ->name('applications.index');

    Route::get('/applications/data', [AgentApplicationController::class, 'data'])
        ->name('applications.data');

    Route::get('/applications/{application}', [AgentApplicationController::class, 'show'])
        ->name('applications.show');

    Route::post('/applications/{application}/retry', [AgentApplicationController::class, 'retry'])
        ->name('applications.retry');

    Route::patch('/applications/{application}/cancel', [AgentApplicationController::class, 'cancel'])
        ->name('applications.cancel');

    Route::get('/commissions', [CommissionController::class, 'commissions'])
        ->name('commissions');

    Route::get('/commissions/table', [CommissionController::class, 'commissionsTable'])
        ->name('commissions.table');

    Route::get('/payouts', [CommissionController::class, 'payouts'])
        ->name('payouts');

    Route::get('/payouts/table', [CommissionController::class, 'payoutsTable'])
        ->name('payouts.table');

    Route::get('/documents/{media}', [AgentApplicationController::class, 'viewDocument'])
        ->name('documents.view');

    Route::get('/documents/{media}/download', [AgentApplicationController::class, 'downloadDocument'])
        ->name('documents.download');

    Route::get('/applications/{id}/balance-sheet', [AgentApplicationController::class, 'balanceSheetForm'])->name('applications.balance-sheet');
    Route::post('/applications/{id}/balance-sheet/generate', [AgentApplicationController::class, 'generateBalanceSheetPdf'])->name('applications.balance-sheet.generate');

});

// ==========================================
//  INTERNAL TEAM DASHBOARD ROUTES
// ==========================================
Route::middleware(['auth', 'team'])->prefix('team')->name('team.')->group(function () {

    // Core Dashboard & Task View
    Route::get('/dashboard', [\App\Http\Controllers\Team\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/applications/{id}', [\App\Http\Controllers\Team\DashboardController::class, 'show'])->name('applications.show');
    Route::post('/applications/{id}/status', [\App\Http\Controllers\Team\DashboardController::class, 'updateStatus'])->name('applications.status');

    // Document Management (For Operators)
    Route::post('/applications/{id}/document', [\App\Http\Controllers\Team\DashboardController::class, 'uploadDocument'])->name('applications.uploadDocument');
    Route::delete('/documents/{id}', [\App\Http\Controllers\Team\DashboardController::class, 'deleteDocument'])->name('applications.deleteDocument');
    Route::get('/documents/{id}/view', [\App\Http\Controllers\Team\DashboardController::class, 'viewDocument'])->name('documents.view');
    Route::get('/documents/{id}/download', [\App\Http\Controllers\Team\DashboardController::class, 'downloadDocument'])->name('documents.download');

    // Balance Sheet Generation (For Operators)
    Route::get('/applications/{id}/balance-sheet', [\App\Http\Controllers\Team\DashboardController::class, 'balanceSheet'])->name('applications.balance-sheet');
    Route::post('/applications/{id}/balance-sheet', [\App\Http\Controllers\Team\DashboardController::class, 'generateBalanceSheet'])->name('applications.balance-sheet.generate');

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
//

Route::middleware(['auth', 'admin', 'sidebar'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('/applications/{id}/assign', [\App\Http\Controllers\Admin\ApplicationController::class, 'assignTeam'])->name('applications.assign');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/switch-server/{target}', [AdminDashboardController::class, 'switchServer'])
        ->name('switch_server');

    // ==========================================
    // ADMIN: TEAM / OPERATOR MANAGEMENT
    // ==========================================
    Route::get('/team', [\App\Http\Controllers\Admin\TeamController::class, 'index'])->name('team.index');

    Route::get('/team/create', [\App\Http\Controllers\Admin\TeamController::class, 'create'])->name('team.create');
    Route::get('/team/{id}/edit', [\App\Http\Controllers\Admin\TeamController::class, 'edit'])->name('team.edit');

    Route::post('/team', [\App\Http\Controllers\Admin\TeamController::class, 'store'])->name('team.store');
    Route::put('/team/{id}', [\App\Http\Controllers\Admin\TeamController::class, 'update'])->name('team.update');

    Route::patch('/team/{id}/toggle-status', [\App\Http\Controllers\Admin\TeamController::class, 'toggleStatus'])->name('team.toggle-status');

    Route::delete('/team/{id}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])->name('team.destroy');

    Route::get('/team/{id}/profile', [\App\Http\Controllers\Admin\TeamController::class, 'show'])->name('team.show');
    Route::post('/team/{id}/rates', [\App\Http\Controllers\Admin\TeamController::class, 'saveRates'])->name('team.save-rates');
    Route::get('/team/{id}/payout/create', [\App\Http\Controllers\Admin\TeamController::class, 'createPayout'])->name('team.create-payout');
    Route::post('/team/{id}/payout', [\App\Http\Controllers\Admin\TeamController::class, 'addPayout'])->name('team.add-payout');

    // Remote KPIs removed as requested

    Route::get('/applications', [AdminApplicationController::class, 'index'])
        ->name('applications.index');

    Route::get('/applications/data', [AdminApplicationController::class, 'data'])
        ->name('applications.data');

    Route::get('/applications/export', [AdminApplicationController::class, 'export'])
        ->name('applications.export');

    Route::post('/applications/bulk', [AdminApplicationController::class, 'bulk'])
        ->name('applications.bulk');

    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])
        ->name('applications.show');

    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy'])
        ->name('applications.destroy');

    Route::post('/applications/{id}/restore', [AdminApplicationController::class, 'restore'])
        ->name('applications.restore');

    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])
        ->name('applications.updateStatus');

    Route::patch('/applications/{application}/payment-status', [AdminApplicationController::class, 'updatePaymentStatus'])
        ->name('applications.updatePaymentStatus');

    Route::post('/applications/{application}/documents', [\App\Http\Controllers\Admin\ApplicationController::class, 'uploadDocument'])->name('applications.uploadDocument');
    Route::delete('/applications/documents/{media}', [\App\Http\Controllers\Admin\ApplicationController::class, 'deleteDocument'])->name('applications.deleteDocument');

    Route::get('/applications/{application}/export-single', [AdminApplicationController::class, 'exportSingle'])
        ->name('applications.exportSingle');

    Route::post('/applications/{application}/credentials', [\App\Http\Controllers\Admin\ApplicationController::class, 'storeCredentials'])->name('applications.storeCredentials');

    Route::get('/applications/{id}/balance-sheet', [App\Http\Controllers\Admin\ApplicationController::class, 'balanceSheetForm'])->name('applications.balance-sheet');

    Route::post('/applications/{id}/balance-sheet/generate', [App\Http\Controllers\Admin\ApplicationController::class, 'generateBalanceSheetPdf'])->name('applications.balance-sheet.generate');

    /*
    |--------------------------------------------------------------------------
    | Payouts
    |--------------------------------------------------------------------------
    */

    Route::get('/payouts', [PayoutController::class, 'index'])
        ->name('payouts.index');

    Route::get('/payouts/table', [PayoutController::class, 'table'])
        ->name('payouts.table');

    Route::post('/payouts/preview', [PayoutController::class, 'preview'])
        ->name('payouts.preview');

    Route::post('/payouts/generate', [PayoutController::class, 'generate'])
        ->name('payouts.generate');

    Route::post('/payouts/{payout}/paid', [PayoutController::class, 'markPaid'])
        ->name('payouts.markPaid');

    Route::get('/payouts/{payout}', [PayoutController::class, 'show'])
        ->name('payouts.show');

    /*
    |--------------------------------------------------------------------------
    | Agents
    |--------------------------------------------------------------------------
    */

    Route::get('/agents', [AgentController::class, 'index'])
        ->name('agents.index');

    Route::get('/agents/datatable', [AgentController::class, 'datatable'])
        ->name('agents.datatable');

    Route::get('/agents/create', [AgentController::class, 'create'])
        ->name('agents.create');

    Route::post('/agents', [AgentController::class, 'store'])
        ->name('agents.store');

    Route::get('/agents/{agent}', [AgentController::class, 'show'])
        ->name('agents.show');

    Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])
        ->name('agents.edit');

    Route::put('/agents/{agent}', [AgentController::class, 'update'])
        ->name('agents.update');

    Route::patch('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])
        ->name('agents.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');

    /*
    |--------------------------------------------------------------------------
    | B2B Only Modules (Services, Pages, Gifts)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['b2b.only'])->group(function () {
        // Services
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/datatable', [AdminServiceController::class, 'datatable'])->name('services.datatable');
        Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}', [AdminServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        Route::patch('/services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        
        // Static Pages
        Route::get('/pages/datatable', [\App\Http\Controllers\Admin\PageController::class, 'datatable'])->name('pages.datatable');
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->except(['show']);
        Route::patch('/pages/{page}/toggle', [\App\Http\Controllers\Admin\PageController::class, 'toggle'])->name('pages.toggle');

        // Gifts
        Route::get('gifts/eligibility', [\App\Http\Controllers\Admin\GiftEligibilityController::class, 'hub'])->name('gifts.eligibility.hub');
        Route::resource('gifts', \App\Http\Controllers\Admin\GiftController::class)->except(['show']);
        Route::get('gifts/{gift}/eligibility', [\App\Http\Controllers\Admin\GiftEligibilityController::class, 'index'])->name('gifts.eligibility');
    });

    Route::get('/documents/{media}', [AdminApplicationController::class, 'viewDocument'])
        ->name('documents.view');

    Route::get('/documents/{media}/download', [AdminApplicationController::class, 'downloadDocument'])
        ->name('documents.download');
    Route::post('/applications/{id}/assign-team', [\App\Http\Controllers\Admin\ApplicationController::class, 'assignTeam'])->name('applications.assign-team');

    // Promo & Coupon Management
    Route::get('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [\App\Http\Controllers\Admin\CouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');

});

/*
|--------------------------------------------------------------------------
| CRM & Marketing Routes (Shared by Admin & Marketers)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'sidebar'])->prefix('crm')->name('crm.')->group(function () {

    // --- Marketers UI ---
    Route::get('/marketers', [\App\Http\Controllers\Admin\MarketerController::class, 'index'])->name('marketers.index');
    Route::get('/marketers/datatable', [\App\Http\Controllers\Admin\MarketerController::class, 'datatable'])->name('marketers.datatable');

    // CRITICAL: create route moved ABOVE wildcard routes
    Route::get('/marketers/create', [\App\Http\Controllers\Admin\MarketerController::class, 'create'])->name('marketers.create');

    Route::post('/marketers', [\App\Http\Controllers\Admin\MarketerController::class, 'store'])->name('marketers.store');

    Route::get('/marketers/{id}/edit', [\App\Http\Controllers\Admin\MarketerController::class, 'edit'])->name('marketers.edit');
    Route::put('/marketers/{id}', [\App\Http\Controllers\Admin\MarketerController::class, 'update'])->name('marketers.update');
    Route::delete('/marketers/{id}', [\App\Http\Controllers\Admin\MarketerController::class, 'destroy'])->name('marketers.destroy');

    Route::patch('/marketers/{user}/toggle-status', [\App\Http\Controllers\Admin\MarketerController::class, 'toggleStatus'])->name('marketers.toggle-status');

    // --- Leads UI ---
    Route::get('/leads/vle', [\App\Http\Controllers\Admin\LeadController::class, 'indexVle'])->name('leads.vle.index');
    Route::get('/leads/vle/data', [\App\Http\Controllers\Admin\LeadController::class, 'vleDatatable'])->name('leads.vle.data');
    Route::get('/leads/vle/create', [\App\Http\Controllers\Admin\LeadController::class, 'createVle'])->name('leads.vle.create');
    Route::post('/leads/vle', [\App\Http\Controllers\Admin\LeadController::class, 'storeVle'])->name('leads.vle.store');
    Route::get('/leads/{lead}/edit', [\App\Http\Controllers\Admin\LeadController::class, 'edit'])->name('leads.edit');
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/datatable', [\App\Http\Controllers\Admin\LeadController::class, 'datatable'])->name('leads.datatable');

    // CRITICAL: create route moved ABOVE wildcard routes
    Route::get('/leads/create', [\App\Http\Controllers\Admin\LeadController::class, 'create'])->name('leads.create');

    Route::post('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'store'])->name('leads.store');
    Route::patch('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'update'])->name('leads.update');

    // Removed the "show" route entirely because we use a modal, not a show page!
});

/*
|--------------------------------------------------------------------------
| Marketer Dashboard Route
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'sidebar'])->group(function () {
    Route::get('/marketer/dashboard', [\App\Http\Controllers\Admin\LeadController::class, 'dashboard'])->name('marketer.dashboard');
    Route::get('/marketer/vle/create', [\App\Http\Controllers\Admin\LeadController::class, 'createVle'])->name('marketer.vle.create');
    Route::post('/marketer/vle', [\App\Http\Controllers\Admin\LeadController::class, 'storeVle'])->name('marketer.vle.store');
});
/*
|--------------------------------------------------------------------------
| Profile (Both Roles)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/

// Route::middleware('auth')->group(function () {

//     Route::post('/applications/{application}/retry-payment', [ApplicationController::class, 'retryPayment'])
//         ->name('applications.retryPayment');

//     Route::get('/payment/result', [ApplicationController::class, 'result'])
//         ->name('payment.result');

// });

// Route::match(['GET', 'POST'], '/payment/redirect', [ApplicationController::class, 'redirect'])
//     ->name('payment.redirect');

// Route::post('/payment/webhook', [ApplicationController::class, 'webhook'])
//     ->name('payment.webhook');

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/applications/{application}/retry-payment', [ApplicationController::class, 'retryPayment'])
        ->name('applications.retryPayment');

    Route::post('/payment/success', [ApplicationController::class, 'paymentSuccess'])
        ->name('payment.success');

    Route::post('/payment/failure', [ApplicationController::class, 'paymentFailure'])
        ->name('payment.failure');

    Route::get('/payment/result', [ApplicationController::class, 'result'])
        ->name('payment.result');

    Route::get('/payment/status/{transactionId}', [ApplicationController::class, 'checkStatus'])
        ->name('payment.status');
});

Route::post('/payment/webhook', [ApplicationController::class, 'webhook'])
    ->name('payment.webhook');

// B2B Sync routes removed as requested

/*
|--------------------------------------------------------------------------
| Global Fallback Route (Catches all 404 Route Errors)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return redirect()->route('login');
});
