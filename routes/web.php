<?php

use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/Visits/checkin/qr/{visit}', [VisitController::class, 'checkInViaQr'])
    ->name('visits.checkin.qr')
    ->middleware('signed');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'check.role:admin,employee'])
    ->name('dashboard');

// NDA Routes voor visitors
Route::middleware(['auth', 'check.role:visitor'])->group(function () {
    Route::get('/visits/{visit}/nda', [VisitController::class, 'showNdaPage'])
        ->name('visitor.nda.show');

    Route::post('/visits/{visit}/nda/accept', [VisitController::class, 'acceptNda'])
        ->name('visitor.nda.accept');
});

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::put('settings/appearance', [Settings\AppearanceController::class, 'update'])->name('settings.appearance.update');
});

Route::get('/Visits/my', [VisitController::class, 'myvisits'])
    ->middleware(['auth', 'check.role:employee,visitor'])
    ->name('visits.myvisits');
Route::get('/Visits/history', [VisitController::class, 'history'])
    ->middleware(['auth', 'check.role:admin,employee'])
    ->name('visits.history');
Route::get('/Visits/export', [VisitController::class, 'export'])
    ->middleware(['auth', 'check.role:admin,employee'])
    ->name('visits.export');
Route::get('/Visits/{visit}', [VisitController::class, 'show'])
    ->whereNumber('visit')
    ->name('visits.show')
    ->middleware(['auth', 'check.role:admin,employee,visitor']);

Route::middleware(['auth', 'check.role:admin,employee'])->group(function () {
    // Bezoeken
    Route::get('/Visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/Visits/active', [VisitController::class, 'active'])->name('visits.active');
    Route::get('/Visits/active/export', [VisitController::class, 'activeExport'])->name('visits.active.export');
    Route::get('/Visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/Visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/Visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
    Route::put('/Visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
    Route::delete('/Visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
    Route::match(['get', 'post'], '/Visits/checkin/{visit}', [VisitController::class, 'checkIn'])->name('visits.checkin');
    Route::get('/Visits/checkout/{visit}', [VisitController::class, 'checkOut'])->name('visits.checkout');
});

// Medewerkers
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/Employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/Employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/Employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/Employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/Employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/Employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/Employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

// Afdelingen
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/Departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/Departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/Departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/Departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::get('/Departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/Departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/Departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
});

// Gebruikers
Route::middleware(['auth', 'check.role:admin,employee'])->group(function () {
    Route::get('/Users', [UserController::class, 'index'])->name('users.index');
    Route::get('/Users/{user}', [UserController::class, 'show'])->name('users.show');
});

Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/Users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/Users', [UserController::class, 'store'])->name('users.store');
    Route::get('/Users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/Users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/Users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Bezoekers
Route::middleware(['auth', 'check.role:admin,employee'])->group(function () {
    Route::get('/Visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('/Visitors/create', [VisitorController::class, 'create'])->name('visitors.create');
    Route::post('/Visitors', [VisitorController::class, 'store'])->name('visitors.store');
    Route::get('/Visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
    Route::get('/Visitors/{visitor}/edit', [VisitorController::class, 'edit'])->name('visitors.edit');
    Route::put('/Visitors/{visitor}', [VisitorController::class, 'update'])->name('visitors.update');
    Route::delete('/Visitors/{visitor}', [VisitorController::class, 'destroy'])->name('visitors.destroy');
});

// Notificaties
Route::middleware(['auth', 'check.role:admin,employee,visitor'])->group(function () {
    Route::get('/Notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/Notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/Notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/Notifications/{notification}/edit', [NotificationController::class, 'edit'])->name('notifications.edit');
    Route::put('/Notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/Notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// API Routes for Notifications
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::get('/notifications', [ApiNotificationController::class, 'index'])->name('api.notifications.index');
    Route::get('/notifications/unread', [ApiNotificationController::class, 'unread'])->name('api.notifications.unread');
    Route::post('/notifications/{id}/read', [ApiNotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [ApiNotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
    Route::delete('/notifications/{id}', [ApiNotificationController::class, 'destroy'])->name('api.notifications.destroy');
    Route::delete('/notifications', [ApiNotificationController::class, 'destroyAll'])->name('api.notifications.destroy-all');
});

// Mailbox
Route::middleware(['auth', 'check.role:admin,employee,visitor'])->group(function () {
    Route::get('/Mailbox', [MailboxController::class, 'index'])->name('mailbox.index');
    Route::get('/Mailbox/create', [MailboxController::class, 'create'])->name('mailbox.create');
    Route::post('/Mailbox', [MailboxController::class, 'store'])->name('mailbox.store');
    Route::get('/Mailbox/{mailboxMessage}', [MailboxController::class, 'show'])->name('mailbox.show');
    Route::delete('/Mailbox/{mailboxMessage}', [MailboxController::class, 'destroy'])->name('mailbox.destroy');
});

require __DIR__ . '/auth.php';
// niet meer veranderen nu T_T
