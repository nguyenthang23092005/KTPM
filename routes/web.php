<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\HiringController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\RecruitmentController;
use App\Http\Controllers\NotificationController;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\RecruitmentPeriod;
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
    $supportsRecruitmentPeriods = Schema::hasTable('recruitment_periods')
        && Schema::hasColumn('job_postings', 'recruitment_period_id');
    $isAdminViewer = auth()->check() && auth()->user()->role === 'admin';

    $selectedPeriodId = trim((string) request()->query('period_id', ''));
    $periods = collect();

    if ($supportsRecruitmentPeriods) {
        $periodsQuery = RecruitmentPeriod::query();

        if (!$isAdminViewer) {
            $periodsQuery->whereIn('status', ['open', 'closed']);
        }

        $periods = $periodsQuery
            ->withCount([
                'jobPostings as active_jobs_count' => function ($query) {
                    $query->where('status', 'active');

                    if (Schema::hasColumn('job_postings', 'is_deleted')) {
                        $query->where('is_deleted', false);
                    }
                },
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('updated_at')
            ->get();
    }

    $selectedPeriod = $selectedPeriodId !== ''
        ? $periods->firstWhere('period_id', $selectedPeriodId)
        : null;

    $jobsQuery = JobPosting::query()
        ->where('status', 'active');

    if (Schema::hasColumn('job_postings', 'is_deleted')) {
        $jobsQuery->where('is_deleted', false);
    }

    if ($supportsRecruitmentPeriods) {
        $jobsQuery->with('recruitmentPeriod');

        if (!$isAdminViewer) {
            $jobsQuery->where(function ($query) {
                $query->whereNull('recruitment_period_id')
                    ->orWhereHas('recruitmentPeriod', function ($periodQuery) {
                        $periodQuery->whereIn('status', ['open', 'closed']);
                    });
            });
        }

        if ($selectedPeriod) {
            $jobsQuery->where('recruitment_period_id', $selectedPeriod->period_id);
        }
    }

    $jobs = $jobsQuery
        ->orderByRaw('CASE WHEN deadline IS NOT NULL AND deadline < CURRENT_DATE THEN 1 ELSE 0 END')
        ->orderBy('deadline')
        ->paginate(10)
        ->withQueryString();

    return view('jobs.index', compact('jobs', 'periods', 'selectedPeriod', 'supportsRecruitmentPeriods'));
})->name('jobs.index');

Route::get('/jobs/{id}', function ($id) {
    $supportsRecruitmentPeriods = Schema::hasTable('recruitment_periods')
        && Schema::hasColumn('job_postings', 'recruitment_period_id');
    $isAdminViewer = auth()->check() && auth()->user()->role === 'admin';

    $jobQuery = \App\Models\JobPosting::query();

    if ($supportsRecruitmentPeriods) {
        $jobQuery->with('recruitmentPeriod');
    }

    $job = $jobQuery->findOrFail($id);
    $isRecruitmentPeriodDraft = false;
    $isRecruitmentPeriodClosed = false;

    if ($supportsRecruitmentPeriods && $job->recruitmentPeriod) {
        $isRecruitmentPeriodDraft = $job->recruitmentPeriod->status === 'draft';

        if ($isRecruitmentPeriodDraft && !$isAdminViewer) {
            abort(404);
        }

        $isRecruitmentPeriodClosed = $job->recruitmentPeriod->status === 'closed'
            || ($job->recruitmentPeriod->end_date && $job->recruitmentPeriod->end_date->isPast());
    }

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

    return view('jobs.show', compact('job', 'existingApplication', 'isRecruitmentPeriodClosed', 'isRecruitmentPeriodDraft'));
})->name('jobs.show');

Route::post('/apply', function () {
    $validated = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'job_id' => 'required|exists:job_postings,job_id',
        'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
    ]);

    $job = JobPosting::findOrFail($validated['job_id']);

    $supportsRecruitmentPeriods = Schema::hasTable('recruitment_periods')
        && Schema::hasColumn('job_postings', 'recruitment_period_id');

    if ($supportsRecruitmentPeriods) {
        $job->loadMissing('recruitmentPeriod');

        if ($job->recruitmentPeriod && $job->recruitmentPeriod->status === 'draft') {
            return back()
                ->withErrors(['cv' => 'Kì tuyển dụng này chưa mở. Vui lòng quay lại sau.'])
                ->withInput();
        }

        $isRecruitmentPeriodClosed = $job->recruitmentPeriod
            && ($job->recruitmentPeriod->status === 'closed'
                || ($job->recruitmentPeriod->end_date && $job->recruitmentPeriod->end_date->isPast()));

        if ($isRecruitmentPeriodClosed) {
            return back()
                ->withErrors(['cv' => 'Kì tuyển dụng đã kết thúc vui lòng chờ các kì tuyển dụng sắp tới'])
                ->withInput();
        }
    }

    if ($job->isDeleted() || $job->status !== 'active') {
        return back()
            ->withErrors(['cv' => 'Tin tuyển dụng này không còn nhận hồ sơ.'])
            ->withInput();
    }

    if ($job->isDeadlinePassed()) {
        return back()
            ->withErrors(['cv' => 'Vị trí này đã hết hạn tuyển dụng, bạn không thể nộp hồ sơ.'])
            ->withInput();
    }

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

    $positionApplied = $job->title;

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
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('reset_password');
    Route::post('/reset-password', [AuthController::class, 'handleReset'])->name('reset_password.submit');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ===== PROTECTED ROUTES ====
Route::middleware(['auth'])->group(function () {
    // Internal dashboard (admin/staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Staff management overview for admin/staff
        Route::get('/staff', [StaffController::class, 'departmentOverview'])->name('staff.index');
        Route::get('/staff/list', [StaffController::class, 'index'])->name('staff.list');
    });

    // Admin-only hiring actions
    Route::middleware('role:admin')->group(function () {
        Route::get('/hiring-promotions', [HiringController::class, 'index'])
            ->name('hiring.index');
        Route::post('/hiring-promotions/{candidateId}/promote', [HiringController::class, 'promote'])
            ->name('hiring.promote');
    });

    // Staff actions: admin + staff đều vào được, controller tự chặn staff thường / trưởng phòng
    Route::middleware('role:admin,staff')->group(function () {
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

    // Staff edit/update - chỉ admin hoặc staff
    Route::middleware('role:admin,staff')->group(function () {
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

        Route::get('/candidate/{candidateId}/cv', [RecruitmentController::class, 'serveCandidateCv'])
            ->middleware('role:admin,staff')
            ->name('candidateCv');

        // Admin-only recruitment mutations
        Route::middleware('role:admin')->group(function () {
            // Recruitment Periods
            Route::post('/period', [RecruitmentController::class, 'storePeriod'])->name('storePeriod');
            Route::post('/period/{periodId}', [RecruitmentController::class, 'updatePeriod'])->name('updatePeriod');
            Route::delete('/period/{periodId}', [RecruitmentController::class, 'destroyPeriod'])->name('destroyPeriod');


            Route::post('/candidate', [RecruitmentController::class, 'storeCandidate'])->name('storeCandidate');
            Route::delete('/candidate/{candidateId}', [RecruitmentController::class, 'destroyCandidate'])->name('destroyCandidate');
        });

        Route::middleware('role:admin,staff')->group(function () {
            // Job Postings
            Route::post('/job', [RecruitmentController::class, 'storeJob'])->name('storeJob');
            Route::post('/job/{jobId}', [RecruitmentController::class, 'updateJob'])->name('updateJob');
            Route::delete('/job/{jobId}', [RecruitmentController::class, 'destroyJob'])->name('destroyJob');

            // Candidates
            Route::post('/candidate/{candidateId}', [RecruitmentController::class, 'updateCandidate'])->name('updateCandidate');

            // Interviews
            Route::post('/interview', [RecruitmentController::class, 'storeInterview'])->name('storeInterview');
            Route::post('/interview/{interviewId}', [RecruitmentController::class, 'updateInterview'])->name('updateInterview');
            Route::delete('/interview/{interviewId}', [RecruitmentController::class, 'destroyInterview'])->name('destroyInterview');
        });
    });



    // Notification routes
    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unreadCount');
    });
});

