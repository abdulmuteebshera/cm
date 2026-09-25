<?php

use Illuminate\Support\Facades\Route;

/*
| Email Campaign — /emailcampaign/*
| Separate from portal admin and CRM.
*/

Route::controller('Auth\AdminLoginController')->prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('ec.admin.guest')->group(function (): void {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login');
    });
    Route::post('logout', 'logout')->name('logout')->middleware('ec.admin');
});

Route::controller('Auth\UserLoginController')->name('user.')->group(function (): void {
    Route::middleware('ec.user.guest')->group(function (): void {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login');
    });
    Route::post('logout', 'logout')->name('logout')->middleware('ec.user');
});

Route::middleware('ec.admin')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('dashboard', 'Admin\DashboardController@index')->name('dashboard');

    Route::controller('Admin\TemplateController')->prefix('templates')->name('templates.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('{id}/preview', 'preview')->name('preview');
        Route::get('{id}/preview/frame', 'previewFrame')->name('preview_frame');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'destroy')->name('destroy');
    });

    Route::controller('Admin\MailSettingController')->group(function (): void {
        Route::get('mail-settings', 'edit')->name('mail.edit');
        Route::post('mail-settings', 'update')->name('mail.update');
    });

    Route::controller('Admin\UserController')->prefix('users')->name('users.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
    });

    Route::controller('Admin\GroupController')->prefix('groups')->name('groups.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::post('{id}/update', 'update')->name('update');
        Route::post('{id}/members', 'addMember')->name('members.add');
        Route::post('{id}/import', 'importMembers')->name('members.import');
        Route::post('{id}/members/{memberId}/delete', 'destroyMember')->name('members.destroy');
    });

    Route::controller('Admin\CampaignController')->prefix('campaigns')->name('campaigns.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/manage', 'manage')->name('manage');
        Route::post('{id}/content', 'updateContent')->name('content');
        Route::post('{id}/load-template', 'loadTemplate')->name('load_template');
        Route::post('{id}/recipients', 'addRecipient')->name('recipients.add');
        Route::post('{id}/import', 'importRecipients')->name('recipients.import');
        Route::post('{id}/sync-group', 'pullFromGroup')->name('sync_group');
        Route::post('{id}/start', 'start')->name('start');
        Route::post('{id}/pause', 'pause')->name('pause');
        Route::post('{id}/resume', 'resume')->name('resume');
        Route::get('{id}/status', 'statusJson')->name('status');
        Route::get('{id}', 'show')->name('show');
    });

    Route::get('activity', 'Admin\ActivityController@index')->name('activity');
});

Route::middleware('ec.user')->name('user.')->group(function (): void {
    Route::get('dashboard', 'User\DashboardController@index')->name('dashboard');

    Route::controller('User\GroupController')->prefix('groups')->name('groups.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::post('{id}/members', 'addMember')->name('members.add');
        Route::post('{id}/import', 'importMembers')->name('members.import');
        Route::post('{id}/members/{memberId}/delete', 'destroyMember')->name('members.destroy');
    });

    Route::controller('User\CampaignController')->prefix('campaigns')->name('campaigns.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::post('{id}/content', 'updateContent')->name('content');
        Route::post('{id}/load-template', 'loadTemplate')->name('load_template');
        Route::post('{id}/recipients', 'addRecipient')->name('recipients.add');
        Route::post('{id}/import', 'importRecipients')->name('recipients.import');
        Route::post('{id}/sync-group', 'pullFromGroup')->name('sync_group');
        Route::post('{id}/start', 'start')->name('start');
        Route::post('{id}/pause', 'pause')->name('pause');
        Route::post('{id}/resume', 'resume')->name('resume');
        Route::get('{id}/status', 'statusJson')->name('status');
    });
});
