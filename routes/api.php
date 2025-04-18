<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//--------------------------- Reset Password  ---------------------------

Route::group([
    'prefix' => 'password',
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::post('reset', 'PasswordResetController@reset');
});


Route::post('getAccessToken', 'AuthController@getAccessToken');
Route::middleware(['auth:api', 'Is_Active'])->group(function () {

    Route::get('dashboard', 'UserController@GetInfoProfile');
    Route::get("dashboard_data", "DashboardController@dashboard_data");

    //------------------------------- Users --------------------------\\
    //------------------------------------------------------------------\\

    Route::get('get_user_auth', 'UserController@GetUserAuth');
    Route::resource('users', 'UserController');
    Route::put('users_switch_activated/{id}', 'UserController@IsActivated');
    Route::get('Get_user_profile', 'UserController@GetInfoProfile');
    Route::put('update_user_profile/{id}', 'UserController@updateProfile');

    //------------------------------- CLIENTS --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('clients', 'ClientController');
    Route::post('clients/import/csv', 'ClientController@import_clients');
    Route::get('get_clients_without_paginate', 'ClientController@Get_Clients_Without_Paginate');
    Route::post('clients/delete/by_selection', 'ClientController@delete_by_selection');
    Route::post('clients_pay_due', 'ClientController@clients_pay_due');
    Route::post('clients_pay_return_due', 'ClientController@pay_sale_return_due');

    //------------------------------- Providers --------------------------\\
    //--------------------------------------------------------------------\\

    Route::resource('providers', 'ProviderController');
    Route::post('providers/import/csv', 'ProviderController@import_providers');
    Route::post('providers/delete/by_selection', 'ProviderController@delete_by_selection');
    Route::post('pay_supplier_due', 'ProviderController@pay_supplier_due');
    Route::post('pay_purchase_return_due', 'ProviderController@pay_purchase_return_due');

    //------------------------------- Permission Groups user -----------\\
    //------------------------------------------------------------------\\

    Route::resource('roles', 'PermissionController');
    Route::resource('roles/check/create_page', 'PermissionController@Check_Create_Page');
    Route::post('roles/delete/by_selection', 'PermissionController@delete_by_selection');


    //------------------------------- Settings ------------------------\\
    //------------------------------------------------------------------\\
    Route::resource('settings', 'SettingController');
    Route::get('get_Settings_data', 'SettingController@getSettings');
    Route::put('pos_settings/{id}', 'SettingController@update_pos_settings');
    Route::get('get_pos_Settings', 'SettingController@get_pos_Settings');

    //------------------------------- Mail Settings ------------------------\\

    Route::put('update_config_mail/{id}', 'MailSettingsController@update_config_mail');
    Route::get('get_config_mail', 'MailSettingsController@get_config_mail');

    // notifications_template
    Route::get('get_sms_template', 'Notifications_Template@get_sms_template');
    Route::put('update_sms_body', 'Notifications_Template@update_sms_body');

    Route::get('get_emails_template', 'Notifications_Template@get_emails_template');
    Route::put('update_custom_email', 'Notifications_Template@update_custom_email');

    //------------------------------- Currencies --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('currencies', 'CurrencyController');
    Route::post('currencies/delete/by_selection', 'CurrencyController@delete_by_selection');

    //------------------------------- Invoices -----------\\
    //------------------------------------------------------------------\\

    Route::resource('invoices', 'InvoiceController');

    //-------------------------- Clear Cache ---------------------------

    Route::get("clear_cache", "SettingController@Clear_Cache");
});

//-------------------------------  Print & PDF ------------------------\\
//------------------------------------------------------------------\\

Route::get('invoice_pdf/{id}', 'InvoiceController@Invoice_pdf');
