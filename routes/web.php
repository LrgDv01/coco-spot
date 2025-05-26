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
use App\Http\Controllers\ForumController;

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

// Route::get('/', function () {
//     $identifyUser = new deviceIdentifierController();
//     $device = $identifyUser->index();
//     return view('welcome', compact('device'));
// })->name('welcome'); // Removed 'redirect.nonlogin' middleware

Route::get('/', function () {
    $identifyUser = new deviceIdentifierController();
    $device = $identifyUser->index();
    return view('welcome', compact('device'));
})->name('welcome')->middleware('redirect.nonlogin');


// Route::get('/home', function () {
//     return view('welcome');
// })->middleware('redirect.nonlogin');


Route::middleware(['redirect.nonlogin', 'guest'])->group(function () { 
// Route::middleware(['guest'])->group(function () { 
    // Route::get('/', function () {
    //     $identifyUser = new deviceIdentifierController();
    //     $device = $identifyUser->index();
    //     return view('welcome', compact('device'));
    // })->name('welcome');

    Route::view('/login', 'auth.login')->name('login')->middleware('device.ismobile');
    Route::get('/check-email', [AuthController::class, 'checkEmail']);
    Route::view('/register', 'auth.register')->name('register'); 
    Route::get('/forgot-password', [AuthController::class,'showRequestForm'])->name('request-form');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::view('/contact', 'frontpage.contact')->name('contact');

    Route::middleware(['web'])->group(function () { 
        Route::post('/loginsubmit', [AuthController::class, 'login']);
        Route::post('/registersubmit', [AuthController::class, 'register']);
        Route::post('/request/reset-link', [AuthController::class, 'requestResetLink'])->name('request.reset-link');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });
});
 
Route::middleware(['auth'])->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('admin.forums');
    Route::post('/forum/post', [ForumController::class, 'storePost'])->name('forum.storePost');
    Route::post('/forum/post/{postId}/comment', [ForumController::class, 'storeComment'])->name('forum.storeComment');
    Route::post('/forum/react/{postId}', [ForumController::class, 'react'])->name('forum.react');
    Route::delete('/forum/post/{postId}', [ForumController::class, 'deletePost'])->name('forum.deletePost');
    Route::delete('/forum/comment/{commentId}', [ForumController::class, 'deleteComment'])->name('forum.deleteComment');
    Route::put('/forum/post/{postId}', [ForumController::class, 'updatePost'])->name('forum.updatePost');
    Route::put('/forum/comment/{commentId}', [ForumController::class, 'updateComment'])->name('forum.updateComment');


    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', [dashboardController::class, 'display_dashboard'])->name('admin.dashboard');
        Route::get('/reports', [ReportsController::class, 'diseaseReports'])->name('admin.reports');
        Route::get('/admin/notifications', [NotificationController::class, 'adminNotif'])->name('admin.notifications'); 
        Route::get('/admin/api/notifications', [NotificationController::class, 'getNotifications'])->name('admin.notifications.get');
        Route::get('/admin/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
        Route::view('/admin/map', 'admin.pages.map_locations')->name('admin.map'); 
        Route::view('/admin/map_simulation', 'admin.pages.map_simulation')->name('admin.map_simulation'); 
        Route::get('/admin/get_map_locations', [MapController::class, 'get_map_locations'])->name('admin.map_locations');
        Route::get('/admin/get_dashboard_info', [dashboardController::class, 'get_dashboard_info'])->name('get_dashboard_info');
        Route::middleware(['web'])->group(function () {
            Route::post('/admin/verified-report', [ServicesController::class, 'adminVerified'])->name('admin.verified-report');
        });
    })->middleware('redirect.nonadmin');
    
    Route::get('/admin/get_map_locations', [MapController::class, 'get_map_locations'])->name('admin.map_locations');
    Route::view('/user/map', 'user.map_locations')->name('user.map'); 
    Route::view('/user/simulation', 'user.map_simulation')->name('.user.map_simulation'); 


    Route::middleware(['farmer'])->group(function () {
        Route::get('/user/farm', [FarmController::class, 'index'])->name('user.farm');
        Route::get('/user/farmer/notifications', [NotificationController::class, 'farmerNotif'])->name('user.farmer.notifications'); 
        Route::get('/user/api/notifications', [NotificationController::class, 'getFarmerNotifications'])->name('user.farmer.notifications.get');
        Route::get('/user/farmer/notifications/{id}/request-details', [NotificationController::class, 'farmerViewDetails'])->name('user.farmer.viewDetails');
        Route::get('/technician/{id}', [ServicesController::class, 'show'])->name('technician.info');
        Route::get('/user/farmer/reports', [ServicesController::class, 'index'])->name('user.farmer.reports');
        Route::get('/user/submit-report/{id}/diagnosis', [ServicesController::class, 'viewReport'])->name('user.submit-report'); 
        Route::get('/user/submitted-report/{id}/diagnosis', [ServicesController::class, 'viewSubmmittedReport'])->name('user.submitted-report');
        Route::get('/user/farmer/settings', [ProfileController::class, 'index'])->name('user.settings');
        Route::middleware(['web'])->group(function () {
            Route::post('/user/add_farm', [FarmController::class, 'store'])->name('farm.store');
            Route::post('/user/farmer/notifications/{id}/{status}/request-details', [ServicesController::class, 'updateStatus'])->name('update.status');
            Route::post('/user/farmer-report', [ServicesController::class, 'farmerReport'])->name('user.farmer-report');
            Route::post('/user/farmer/update-field', [ProfileController::class, 'updateField'])->name('user.farmer.updateField-profile');
            Route::post('/user/farmer/update-profile', [ProfileController::class, 'updateProfile'])->name('user.farmer.update-profile');
            Route::post('/user/farmer/update-profile-image', [ProfileController::class, 'updateProfileImage'])->name('user.farmer.update-profile-image');
        });
    });

    Route::middleware(['technician'])->group(function () {
        Route::get('/user/notifications', [NotificationController::class, 'technicianNotif'])->name('user.notifications');
        Route::get('/user/technician', [NotificationController::class, 'technicianNotif'])->name('user.notif'); 
        Route::get('/user/api/notifications', [NotificationController::class, 'getTechNotifications'])->name('user.notifications.get');
        // Route::get('/admin/notifications/{id}/read', [NotificationController::class, 'markAsReadTechNotif'])->name('admin.notifications.read');
        Route::get('/user/notifications/{id}/request-details', [NotificationController::class, 'techViewDetails'])->name('user.viewDetails');
        Route::get('/user/reports', [ReportsController::class, 'index'])->name('user.reports'); 
        Route::get('/user/monitor', [ReportsController::class, 'monitorList'])->name('user.monitor'); 
        Route::get('/user/monitor/{id}/report-details', [ReportsController::class, 'viewDetails'])->name('user.viewReportDetails');
        Route::get('/user/technician/settings', [ProfileController::class, 'index'])->name('user.settings');
        Route::middleware(['web'])->group(function () {
            Route::post('/user/notifications/{id}/{status}/request-details', [ServicesController::class, 'updateStatus'])->name('update.status');
            Route::post('/user/technician-report', [ReportsController::class, 'technicianReport'])->name('user.technician-report');
            Route::post('/user/technician/update-field', [ProfileController::class, 'updateField'])->name('user.technician.updateField-profile');
            Route::post('/user/technician/update-profile', [ProfileController::class, 'updateProfile'])->name('user.technician.update-profile');
            Route::post('/user/technician/update-profile-image', [ProfileController::class, 'updateProfileImage'])->name('user.technician.update-profile-image');
        });
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
