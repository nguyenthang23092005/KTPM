<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\RecruitmentController;

// Static CSS route
Route::get('/css/app.css', function () {
    $path = resource_path('css/app.css');
    return Response::make(File::get($path), 200, ['Content-Type' => 'text/css']);
});

// ===== PUBLIC ROUTES (Job Seekers) =====
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/jobs', function () {
    $jobs = \App\Models\JobPosting::where('status', 'active')
        ->where('deadline', '>=', now())
        ->paginate(10);
    return view('jobs.index', compact('jobs'));
})->name('jobs.index');

Route::get('/jobs/{id}', function ($id) {
    $job = \App\Models\JobPosting::findOrFail($id);
    return view('jobs.show', compact('job'));
})->name('jobs.show');

Route::post('/apply', function () {
    // Job application submission - validated
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'job_id' => 'required|exists:job_postings,id',
        'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
    ]);
    
    // Create candidate record
    \App\Models\Candidate::create([
        'name' => request('name'),
        'email' => request('email'),
        'phone' => request('phone'),
        'job_id' => request('job_id'),
        'position_applied' => \App\Models\JobPosting::find(request('job_id'))->title,
        'status' => 'new',
    ]);
    
    return back()->with('success', 'Đã nộp đơn thành công. Chúng tôi sẽ liên hệ bạn sớm!');
})->name('jobs.apply');

// ===== AUTH ROUTES =====
Route::group(['as' => 'auth.'], function () {
    // Show forms
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('reset_password');
    
    // Handle form submissions
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/reset-password', [AuthController::class, 'handleReset'])->name('reset_password.submit');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ===== ADMIN ROUTES (Protected) ====
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Staff Management
    Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/{employeeId}', [StaffController::class, 'show'])->name('show');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{employeeId}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::post('/{employeeId}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{employeeId}', [StaffController::class, 'destroy'])->name('destroy');
    });

    // Recruitment Management
    Route::group(['prefix' => 'recruitment', 'as' => 'recruitment.'], function () {
        Route::get('/', [RecruitmentController::class, 'index'])->name('index');
        
        // Job Postings
        Route::post('/job', [RecruitmentController::class, 'storeJob'])->name('storeJob');
        Route::post('/job/{jobId}', [RecruitmentController::class, 'updateJob'])->name('updateJob');
        Route::delete('/job/{jobId}', [RecruitmentController::class, 'destroyJob'])->name('destroyJob');
        
        // Candidates
        Route::post('/candidate', [RecruitmentController::class, 'storeCandidate'])->name('storeCandidate');
        Route::post('/candidate/{candidateId}', [RecruitmentController::class, 'updateCandidate'])->name('updateCandidate');
        Route::delete('/candidate/{candidateId}', [RecruitmentController::class, 'destroyCandidate'])->name('destroyCandidate');
        
        // Interviews
        Route::post('/interview', [RecruitmentController::class, 'storeInterview'])->name('storeInterview');
        Route::post('/interview/{interviewId}', [RecruitmentController::class, 'updateInterview'])->name('updateInterview');
        Route::delete('/interview/{interviewId}', [RecruitmentController::class, 'destroyInterview'])->name('destroyInterview');
        
        // Submit Application (public)
        Route::post('/apply', [RecruitmentController::class, 'submitApplication'])->name('submitApplication');
    });
});



