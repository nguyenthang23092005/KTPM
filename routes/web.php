<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\RecruitmentController;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;

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
    $existingApplication = null;

    if (auth()->check()) {
        $candidate = Candidate::where('user_id', auth()->user()->user_id)
            ->where('job_id', $job->job_id)
            ->first();

        if ($candidate) {
            $cvPath = null;

            if (is_string($candidate->notes) && preg_match('/CV:\s*(.+)$/', $candidate->notes, $matches)) {
                $cvPath = trim($matches[1]);
            }

            $existingApplication = [
                'status' => $candidate->status,
                'applied_date' => $candidate->applied_date,
                'cv_url' => $cvPath ? asset('storage/' . $cvPath) : null,
                'cv_name' => $cvPath ? basename($cvPath) : null,
            ];
        }
    }

    return view('jobs.show', compact('job', 'existingApplication'));
})->name('jobs.show');

Route::post('/apply', function () {
    $validated = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'job_id' => 'required|exists:job_postings,job_id',
        'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
    ]);

    $uploadedCv = request()->file('cv');
    $directory = 'candidates/US';
    $nameSlug = Str::slug($validated['name'], '_');
    $nameSlug = $nameSlug !== '' ? $nameSlug : 'ung_vien';
    $baseName = 'CV_UV_' . $nameSlug;
    $extension = strtolower($uploadedCv->getClientOriginalExtension() ?: 'pdf');
    $fileName = $baseName . '.' . $extension;
    $counter = 1;

    while (Storage::disk('public')->exists($directory . '/' . $fileName)) {
        $counter++;
        $fileName = $baseName . '_' . $counter . '.' . $extension;
    }

    $cvPath = $uploadedCv->storeAs($directory, $fileName, 'public');

    $user = User::where('email', $validated['email'])->first();
    if (!$user) {
        do {
            $generatedUserId = 'US_' . (string) Str::ulid();
        } while (User::where('user_id', $generatedUserId)->exists());

        $user = User::create([
            'user_id' => $generatedUserId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(12)),
            'role' => 'user',
            'phone' => $validated['phone'],
        ]);
    } else {
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);
    }

    $positionApplied = JobPosting::where('job_id', $validated['job_id'])->value('title');

    Candidate::updateOrCreate(
        ['user_id' => $user->user_id],
        [
            'job_id' => $validated['job_id'],
            'position_applied' => $positionApplied,
            'status' => 'Đang chờ',
            'applied_date' => now()->toDateString(),
            'notes' => 'CV: ' . $cvPath,
        ]
    );
    
    return back()->with([
        'success' => 'Đã nộp đơn thành công. Chúng tôi sẽ liên hệ bạn sớm!',
        'uploaded_cv_url' => asset('storage/' . $cvPath),
        'uploaded_cv_name' => $fileName,
    ]);
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

// ===== PROTECTED ROUTES ====
Route::middleware(['auth'])->group(function () {
    // Internal dashboard (admin/staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Staff management overview for admin/staff
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    });

    // Admin-only staff actions
    Route::middleware('role:admin')->group(function () {
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::delete('/staff/{userId}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // Staff read access for admin/staff
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/staff/{userId}/file/{type}', [StaffController::class, 'serveFile'])
            ->where('type', 'avatar|cv|contract')
            ->name('staff.file');

        Route::get('/staff/{userId}', [StaffController::class, 'show'])
            ->name('staff.show');
    });

    // Admin-only staff mutations
    Route::middleware('role:admin')->group(function () {
        Route::get('/staff/{userId}/edit', [StaffController::class, 'edit'])
            ->name('staff.edit');

        Route::put('/staff/{userId}', [StaffController::class, 'update'])
            ->name('staff.update');
    });

    // Recruitment management
    Route::group(['prefix' => 'recruitment', 'as' => 'recruitment.'], function () {
        // Admin and staff can view recruitment dashboard
        Route::get('/', [RecruitmentController::class, 'index'])
            ->middleware('role:admin,staff')
            ->name('index');

        // Admin-only recruitment mutations
        Route::middleware('role:admin')->group(function () {
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

            Route::post('/apply', [RecruitmentController::class, 'submitApplication'])->name('submitApplication');
        });
    });
});

