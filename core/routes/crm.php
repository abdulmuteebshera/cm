<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internal CRM Routes — /internalportal/*
| Separate from /admin. Does not modify live portal admin.
|--------------------------------------------------------------------------
*/

Route::controller('Auth\LoginController')->group(function () {
    // CRM Super Admin
    Route::middleware('crm.guest:crm')->group(function () {
        Route::get('crm', 'showLoginForm')->defaults('portal', 'crm')->name('login');
        Route::post('crm', 'login')->defaults('portal', 'crm');
    });

    // Manager
    Route::middleware('crm.guest:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/', 'showLoginForm')->defaults('portal', 'manager')->name('login');
        Route::post('/', 'login')->defaults('portal', 'manager');
    });

    // Investment Officer / Agent
    Route::middleware('crm.guest:agent')->prefix('agent')->name('agent.')->group(function () {
        Route::get('/', 'showLoginForm')->defaults('portal', 'agent')->name('login');
        Route::post('/', 'login')->defaults('portal', 'agent');
    });

    // Trader
    Route::middleware('crm.guest:trader')->prefix('trader')->name('trader.')->group(function () {
        Route::get('/', 'showLoginForm')->defaults('portal', 'trader')->name('login');
        Route::post('/', 'login')->defaults('portal', 'trader');
    });

    // Finance
    Route::middleware('crm.guest:finance')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/', 'showLoginForm')->defaults('portal', 'finance')->name('login');
        Route::post('/', 'login')->defaults('portal', 'finance');
    });

    Route::post('logout', 'logout')->name('logout')->middleware('crm');
});

// One-click SSO into live /admin (token consume — public, single-use)
Route::get('admin-bridge/consume/{token}', 'AdminBridgeController@consume')->name('admin.bridge.consume');

// Authenticated CRM area
Route::middleware('crm')->group(function () {

    Route::post('admin-desk/enter', 'AdminBridgeController@enter')
        ->name('admin.desk.enter');

    // Dashboards by portal
    Route::controller('DashboardController')->group(function () {
        Route::get('crm/dashboard', 'crm')->middleware('crm:crm')->name('dashboard');
        Route::get('manager/dashboard', 'manager')->middleware('crm:manager')->name('manager.dashboard');
        Route::get('agent/dashboard', 'agent')->middleware('crm:agent')->name('agent.dashboard');
        Route::get('trader/dashboard', 'trader')->middleware('crm:trader')->name('trader.dashboard');
        Route::get('finance/dashboard', 'finance')->middleware('crm:finance')->name('finance.dashboard');
    });

    // Staff (super admin)
    Route::controller('StaffController')->middleware('crm.permission:crm.staff')->prefix('crm/staff')->name('staff.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    // Roles
    Route::controller('RoleController')->middleware('crm.permission:crm.roles')->prefix('crm/roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
    });

    // Clients
    Route::controller('ClientController')->middleware('crm.permission:agent.clients')->prefix('workspace/clients')->name('clients.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
    });

    // Referrals
    Route::controller('ReferralController')->middleware('crm.permission:agent.referrals')->prefix('workspace/referrals')->name('referrals.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
    });

    // Commissions
    Route::controller('CommissionController')->middleware('crm.permission:agent.commissions')->prefix('workspace/commissions')->name('commissions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('generate-retention', 'generateRetention')->name('retention');
    });

    // Officer ranking / goals
    Route::get('workspace/ranking', 'RankingController@index')
        ->middleware('crm.permission:agent.commissions')
        ->name('ranking.index');

    // Pitch decks
    Route::controller('PitchDeckController')->prefix('workspace/pitch-decks')->name('pitch.')->group(function () {
        Route::get('/', 'index')->middleware('crm.permission:agent.pitch_decks')->name('index');
        Route::get('download/{id}', 'download')->middleware('crm.permission:agent.pitch_decks')->name('download');
        Route::post('store', 'store')->middleware('crm.permission:crm.pitch_decks.manage')->name('store');
        Route::post('delete/{id}', 'delete')->middleware('crm.permission:crm.pitch_decks.manage')->name('delete');
    });

    // Trading
    Route::controller('TradingController')->middleware('crm.permission:trader.positions')->prefix('workspace/trading')->name('trading.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
    });

    // Finance
    Route::controller('FinanceController')->middleware('crm.permission:finance.entries')->prefix('workspace/finance')->name('finance.ledger.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('delete/{id}', 'delete')->name('delete');
    });

    // Portal oversight (read-only mirrors — does not replace /admin)
    Route::controller('PortalOverviewController')->prefix('crm/portal')->name('portal.')->group(function () {
        Route::get('investors', 'investors')->middleware('crm.permission:portal.investors')->name('investors');
        Route::get('deposits', 'deposits')->middleware('crm.permission:portal.deposits')->name('deposits');
        Route::get('withdrawals', 'withdrawals')->middleware('crm.permission:portal.withdrawals')->name('withdrawals');
        Route::get('tickets', 'tickets')->middleware('crm.permission:portal.tickets')->name('tickets');
        Route::get('investments', 'investments')->middleware('crm.permission:portal.investments')->name('investments');
        Route::get('jobs', 'jobs')->middleware('crm.permission:portal.jobs')->name('jobs');
    });
});
