<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\SuperAdmin\User;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test', function () {
    return view('welcome');
});
Route::redirect('/', '/login', 301);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home-1', [App\Http\Controllers\HomeController::class, 'index'])->name('home-1');

// Route::get('users', [App\Http\Controllers\SuperAdmin\User::class, 'index'])->name('users');

Route::group(['middleware' => 'auth'], function(){
    Route::group(['namespace' => 'App\Http\Controllers\SuperAdmin', 'prefix' => 'super-admin', 'as' => 'superAdmin.', 'middleware' => ['rolesuperadmin']], function(){
        Route::resource('users', UserController::class);
        Route::get('campaign-group/import-yesterday-data', 'UserController@importYesterdayData')->name('importYesterdayCampaignData');
    });   
    
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['roleadmin']], function(){
        Route::group(['namespace' => 'App\Http\Controllers\Admin'], function(){
            Route::resource('users', UserController::class);
            Route::resource('trackers', TrackerController::class);
            Route::resource('campaigns', CampaignController::class);
            Route::resource('invoices', InvoiceController::class);
            
            Route::post('campaign-group/add-campaign', 'CampaignController@addCampaign')->name('addCampaignToGroup');
            Route::get('campaign-group/get-campaign/{id}', 'CampaignController@getCampaign')->name('getCampaignFromGroup');
            Route::get('campaign-group/delete-campaign/{id}', 'CampaignController@deleteCampaign')->name('deleteCampaignFromGroup');
            Route::post('campaign-group/add-user', 'CampaignController@addUser')->name('addUserToGroup');
            Route::get('campaign-group/{id}/added-users', 'CampaignController@addedUsersToGroup')->name('addedUsersToGroup');
            Route::post('campaign-group/add-credit', 'CampaignController@addCredit')->name('addCreditToGroup');
            Route::get('campaign-group/{id}/list-credit', 'CampaignController@listCredit')->name('listCreditFromGroup');
            Route::get('campaign-group/credit/{id}', 'CampaignController@deleteCredit')->name('deleteCreditFromGroup');
            Route::get('invoices/user/{id}', 'InvoiceController@byUser')->name('invoicesByUser');
            Route::post('invoices/bulk-update', 'InvoiceController@bulkUpdate')->name('invoiceBulkUpdate');
        });   
        Route::group(['namespace' => 'App\Http\Controllers\User'], function(){
            Route::get('dashboard/{userId}', 'DashboardController@index')->name('userDashboard');
            Route::post('campaign-group-stats/{userId}', 'DashboardController@getAllCampaignGroupStats')->name('getAllCampaignGroupStats');
            Route::post('campaign-hourly-stats/{userId}', 'DashboardController@getCampaignHourlyStats')->name('getCampaignHourlyStats');
            Route::post('all-campaign-hourly-stats/{userId}', 'DashboardController@getAllCampaignHourlyStats')->name('getAllCampaignHourlyStats');
        });   
    });   
    
    Route::group(['namespace' => 'App\Http\Controllers\User', 'prefix' => 'user', 'as' => 'user.', 'middleware' => ['roleuser']], function(){
        Route::resource('dashboard', DashboardController::class);
        Route::post('campaign-group-stats', 'DashboardController@getAllCampaignGroupStats')->name('getAllCampaignGroupStats');
        Route::post('campaign-hourly-stats', 'DashboardController@getCampaignHourlyStats')->name('getCampaignHourlyStats');
        Route::post('all-campaign-hourly-stats', 'DashboardController@getAllCampaignHourlyStats')->name('getAllCampaignHourlyStats');
        Route::get('invoices', 'DashboardController@invoices')->name('invoices');
    }); 
    Route::get('campaign-search-stats', [App\Http\Controllers\User\DashboardController::class, 'campaignService'])->name('public.campaingService');
    
    // Route::resource('profile', App\Http\Controllers\User\ProfileController::class);
    
    Route::get('/global-js', function(){
        return response(view('sections.global-js'), 200)
                      ->header('Content-Type', 'application/javascript');
    })->name('global-js');
});

Route::get('check-cron-status', [App\Http\Controllers\User\DashboardController::class, 'checkCronStatus'])->name('public.checkCronStatus');

Route::group(['prefix' => 'service', 'as' => 'service.'], function(){
    Route::group(['prefix' => 'campaign', 'as' => 'campaign.', 'namespace' => 'App\Services'], function(){
        Route::get('get-stats/{campaignId}/{dateFrom}/{dateTo}', 'CampaignService@getCampaignStats')->name('getStats');
        Route::get('store-stats/{campaignId}/{date}', 'CampaignService@storeCampaignStats')->name('storeStats');
        Route::get('store-stats-all/{date}', 'CampaignService@storeAllCampaignStats')->name('storeStatsAll');
        Route::get('store-stats-all-yesterday', 'CampaignService@storeAllCampaignStatsYerterday')->name('storeStatsAllYerterday');
    });
    Route::group(['prefix' => 'invoice', 'as' => 'invoice.', 'namespace' => 'App\Services'], function(){
        Route::get('make/{userId}/{dateFrom}/{dateTo}', 'CampaignService@makeInvoice')->name('makeInvoice');
        Route::get('make-all/{dateFrom}/{dateTo}', 'CampaignService@makeInvoices')->name('makeInvoices');
        Route::get('generate', 'CampaignService@generateInvoices')->name('generateInvoices');
    });

    Route::group(['prefix' => 'cron', 'as' => 'cron.', 'namespace' => 'App\Services'], function(){
        Route::get('every-min', 'CampaignService@everyMinCron')->name('everyMin');
    });

    Route::get('stats-invoice-yesterday', [App\Services\CampaignService::class, 'generateYesterdayStatsAndInvoiceRun'])->name('generateYesterdayStatsAndInvoice');
    Route::get('stats-all-x-days/{day}', [App\Services\CampaignService::class, 'storeAllCampaignStatsXDaysRun'])->name('storeAllCampaignStatsXDays');
});