<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/courses', CourseController::class);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollUserInCourse'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll/{user}', [CourseController::class, 'unenrollUserFromCourse'])->name('courses.unenroll');
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':user'])->group(function () {
    Route::get('/my-courses', [CourseController::class, 'listMyCourses'])->name('mycourses.list');
    Route::get('/my-courses/{course}/edit', [CourseController::class, 'editMyCourse'])->name('mycourses.edit');
});

require __DIR__.'/auth.php';
