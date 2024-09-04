<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
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
    Route::post('/purchase-course', [PaymentController::class, 'purchaseCourse'])->name('purchase.course');


});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/courses', CourseController::class);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollUserInCourse'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll/{user}', [CourseController::class, 'unenrollUserFromCourse'])->name('courses.unenroll');
    Route::get('/courses/{course}/students/search', [CourseController::class, 'searchStudents'])->name('courses.students.search');
    Route::post('/courses/upload-thumbnail', [CourseController::class, 'uploadThumbnail'])->name('courses.upload-thumbnail');

});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':user'])->group(function () {
    Route::get('/my-courses', [CourseController::class, 'listMyCourses'])->name('mycourses.list');
    Route::get('/my-courses/{course}/edit', [CourseController::class, 'editMyCourse'])->name('mycourses.edit');
    Route::get('/showcase', [CourseController::class, 'showcase'])->name('showcase');
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('enroll');

});

require __DIR__.'/auth.php';
