<?php

use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\dashboardController;
use App\Http\Controllers\admin\MapController;
use App\Http\Controllers\deviceIdentifierController;
use App\Http\Controllers\user\FarmController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\user\technician\ReportsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    $identifyUser = new deviceIdentifierController();
    $device = $identifyUser->index();
    return view('welcome', compact('device'));
})->name('home')->middleware('redirect.nonlogin');

Route::get('/home', function () {
    return view('welcome');
})->middleware('redirect.nonlogin');


Route::middleware(['redirect.nonlogin', 'guest'])->group(function () { 

    Route::view('/login', 'auth.login')->name('login')->middleware('device.ismobile');
    Route::view('/register', 'auth.register')->name('register'); 
    Route::post('/loginsubmit', [AuthController::class, 'login']);
    Route::post('/registersubmit', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class,'showRequestForm'])->name('request-form');
    Route::post('/request/reset-link', [AuthController::class, 'requestResetLink'])->name('request.reset-link');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::view('/contact', 'frontpage.contact')->name('contact');

});


Route::middleware(['auth'])->group(function () {
    
    Route::middleware(['admin'])->group(function () {
       // Admin dashboard view with notifications
        Route::get('/admin', [NotificationController::class, 'adminNotif'])->name('admin.dashboard'); 
        Route::get('/admin/api/notifications', [NotificationController::class, 'getNotifications'])->name('admin.notifications.get');
        // Route::get('/admin/api/notifications', [NotificationController::class, 'showNotif'])->name('admin.notifications.get');
        Route::get('/admin/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
        
        Route::get('/admin', [dashboardController::class, 'display_dashboard'])->name('admin.dashboard');
        // Route::get('/admin/map', [MapController::class, 'view_map'])->name('admin.map'); 
        Route::view('/admin/map', 'admin.pages.map_locations')->name('admin.map'); 
        Route::view('/admin/map_simulation', 'admin.pages.map_simulation')->name('admin.map_simulation'); 
        Route::get('/admin/get_map_locations', [MapController::class, 'get_map_locations'])->name('admin.map_locations');
        // Route::get('/admin/get_map_simulation', [MapController::class, 'get_map_simulation'])->name('admin.map_simulation');
        Route::get('/admin/get_dashboard_info', [dashboardController::class, 'get_dashboard_info'])->name('get_dashboard_info');
        // Route::get('/admin/get_dashboard_info', [dashboardController::class, 'get_dashboard_info'])->name('get_dashboard_info');
        Route::middleware(['web'])->group(function () {

        });
    })->middleware('redirect.nonadmin');
    

    Route::middleware(['farmer'])->group(function () {
        Route::get('/user/farm', [FarmController::class, 'index'])->name('user.farm');
        Route::get('/user/services/hired', [ServicesController::class, 'hired'])->name('user.services.hired');
        Route::get('/technician/{id}', [ServicesController::class, 'show'])->name('technician.info');
        Route::get('/user/services', [ServicesController::class, 'index'])->name('user.services');
        Route::get('/user/submit-report/{id}/diagnosis', [ServicesController::class, 'viewReport'])->name('user.submit-report'); 
        Route::get('/user/submitted-report/{id}/diagnosis', [ServicesController::class, 'viewSubmmittedReport'])->name('user.submitted-report');
        Route::get('/user/farmer/settings', [ProfileController::class, 'index'])->name('user.settings');
        
        Route::middleware(['web'])->group(function () {
            Route::post('/user/add_farm', [FarmController::class, 'store'])->name('farm.store');
            Route::post('/user/update/condition', [FarmController::class, 'updateCondition'])->name('update.condition');
            Route::post('/user/hire-technician', [ServicesController::class, 'hireTechnician'])->name('user.hire');
            Route::post('/user/update-field', [ProfileController::class, 'updateField'])->name('user.update-profile');
            Route::post('/user/update-profile', [ProfileController::class, 'updateProfile'])->name('user.update-profile');
            Route::post('/user/farmer-report', [ServicesController::class, 'farmerReport'])->name('user.farmer-report');
        });
    });

    Route::middleware(['technician'])->group(function () {

        Route::get('/user/notifications', [NotificationController::class, 'index'])->name('user.notifications');
        Route::get('/user/notifications/{id}/request-details', [NotificationController::class, 'viewDetails'])->name('user.viewDetails');
        Route::get('/user/reports', [ReportsController::class, 'index'])->name('user.reports');
        Route::get('/user/technician/settings', [ProfileController::class, 'index'])->name('user.settings');

        Route::middleware(['web'])->group(function () {
         
            Route::post('/user/notifications/{id}/{status}/request-details', [ServicesController::class, 'updateStatus'])->name('update.status');
            Route::post('/reports/submit', [ReportsController::class, 'submitReport'])->name('reports.submit');
            Route::post('/user/update-field', [ProfileController::class, 'updateField'])->name('user.update-profile');
            Route::post('/user/update-profile', [ProfileController::class, 'updateProfile'])->name('user.update-profile');
        });
    });

});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
