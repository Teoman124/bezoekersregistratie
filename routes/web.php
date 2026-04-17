<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

// Bezoeken
Route::get('/visits', [VisitController::class, 'index'])->name('visit.index');
Route::get('/visits/create', [VisitController::class, 'create'])->name('visit.create');
Route::post('/visits', [VisitController::class, 'store'])->name('visit.store');
Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visit.show');
Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visit.edit');
Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visit.update');
Route::delete('/visits/{visit}', [VisitController::class, 'destroy'])->name('visit.destroy');
Route::get('/visits/checkin/{visit}', [VisitController::class, 'checkin'])->name('visit.checkin');
Route::get('/visits/checkout/{visit}', [VisitController::class, 'checkout'])->name('visit.checkout');

// Medewerkers
Route::get('employees', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('employees/create', [EmployeeController::class, 'create'])->name('employee.create');
Route::post('employees', [EmployeeController::class, 'store'])->name('employee.store');
Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employee.show');
Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

// Afdelingen
Route::get('departments', [DepartmentController::class, 'index'])->name('department.index');
Route::get('departments/create', [DepartmentController::class, 'create'])->name('department.create');
Route::post('departments', [DepartmentController::class, 'store'])->name('department.store');
Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('department.show');
Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('department.update');
Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');

// Gebruikers
Route::get('users', [UserController::class, 'index'])->name('user.index');
Route::get('users/create', [UserController::class, 'create'])->name('user.create');
Route::post('users', [UserController::class, 'store'])->name('user.store');
Route::get('users/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::put('users/{user}', [UserController::class, 'update'])->name('user.update');
Route::delete('users/{user}', [UserController::class, 'destroy'])->name('user.destroy');

// Bezoekers
Route::get('visitors', [VisitorController::class, 'index'])->name('visitor.index');
Route::get('visitors/create', [VisitorController::class, 'create'])->name('visitor.create');
Route::post('visitors', [VisitorController::class, 'store'])->name('visitor.store');
Route::get('visitors/{visitor}', [VisitorController::class, 'show'])->name('visitor.show');
Route::get('visitors/{visitor}/edit', [VisitorController::class, 'edit'])->name('visitor.edit');
Route::put('visitors/{visitor}', [VisitorController::class, 'update'])->name('visitor.update');
Route::delete('visitors/{visitor}', [VisitorController::class, 'destroy'])->name('visitor.destroy');

// notificaties
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');



