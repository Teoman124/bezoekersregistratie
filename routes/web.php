<?php

use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\NotificationController;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function () {
    return view('dashboard', [
        'stats' => [
            'users' => User::count(),
            'employees' => Employee::count(),
            'visitors' => Visitor::count(),
            'visits' => Visit::count(),
            'departments' => Department::count(),
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::put('settings/appearance', [Settings\AppearanceController::class, 'update'])->name('settings.appearance.update');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Bezoeken
    Route::get('/Visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/Visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/Visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/Visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
    Route::get('/Visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
    Route::put('/Visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
    Route::delete('/Visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
    Route::match(['get', 'post'], '/Visits/checkin/{visit}', [VisitController::class, 'checkIn'])->name('visits.checkin');
    Route::get('/Visits/checkout/{visit}', [VisitController::class, 'checkOut'])->name('visits.checkout');

    // Medewerkers
    Route::get('/Employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/Employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/Employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/Employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/Employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/Employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/Employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Afdelingen
    Route::get('/Departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/Departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/Departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/Departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::get('/Departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/Departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/Departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Gebruikers
    Route::get('/Users', [UserController::class, 'index'])->name('users.index');
    Route::get('/Users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/Users', [UserController::class, 'store'])->name('users.store');
    Route::get('/Users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/Users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/Users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/Users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Bezoekers
    Route::get('/Visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('/Visitors/create', [VisitorController::class, 'create'])->name('visitors.create');
    Route::post('/Visitors', [VisitorController::class, 'store'])->name('visitors.store');
    Route::get('/Visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
    Route::get('/Visitors/{visitor}/edit', [VisitorController::class, 'edit'])->name('visitors.edit');
    Route::put('/Visitors/{visitor}', [VisitorController::class, 'update'])->name('visitors.update');
    Route::delete('/Visitors/{visitor}', [VisitorController::class, 'destroy'])->name('visitors.destroy');

    // Notificaties
    Route::get('/Notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/Notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/Notifications/{notification}/edit', [NotificationController::class, 'edit'])->name('notifications.edit');
    Route::put('/Notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::post('/Notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/Notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__ . '/auth.php';
