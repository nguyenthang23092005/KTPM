<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\JobPostingDeletedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RecruitmentController extends Controller
{
    private const JOB_DELETED_NOTE_PREFIX = '[JOB_DELETED]';

    private function hasIsDeletedColumn(): bool
    {
        static $hasColumn = null;

        if ($hasColumn === null) {
            $hasColumn = Schema::hasColumn('job_postings', 'is_deleted');
        }

        return $hasColumn;
    }

    public function index()
    {
        $jobPostingsQuery = JobPosting::query();
        if ($this->hasIsDeletedColumn()) {
            $jobPostingsQuery->orderBy('is_deleted');
        }

        $jobPostings = $jobPostingsQuery
            ->orderByDesc('updated_at')
            ->orderByDesc('job_id')
            ->paginate(10);

        $activeJobPostingsQuery = JobPosting::where('status', 'active');
        if ($this->hasIsDeletedColumn()) {
            $activeJobPostingsQuery->where('is_deleted', false);
        }

        $activeJobPostings = $activeJobPostingsQuery
            ->orderBy('title')
            ->get();
        $candidatesQuery = Candidate::with(['user', 'job']);
        if ($this->hasIsDeletedColumn()) {
            $candidatesQuery->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM job_postings jp WHERE jp.job_id = candidates.job_id AND jp.is_deleted = 1) THEN 1 ELSE 0 END");
        }
        $candidates = $candidatesQuery
            ->orderByDesc('updated_at')
            ->paginate(10);

        $interviewsQuery = Interview::with(['candidate', 'job', 'interviewer']);
        if ($this->hasIsDeletedColumn()) {
            $interviewsQuery->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM candidates c JOIN job_postings jp ON jp.job_id = c.job_id WHERE c.user_id = interviews.user_id AND jp.is_deleted = 1) OR notes LIKE '%[JOB_DELETED]%' THEN 1 ELSE 0 END");
        }
        $interviews = $interviewsQuery
            ->orderByDesc('updated_at')
            ->paginate(10);
        $interviewCandidates = Candidate::with('user')
            ->where('status', 'Phỏng vấn')
            ->get();
        $candidatePositions = Candidate::query()
            ->whereNotNull('position_applied')
            ->where('position_applied', '!=', '')
            ->distinct()
            ->orderBy('position_applied')
            ->pluck('position_applied');
        $employees = Employee::where('status', 'Đang làm')->get();

        return view('recruitment', [
            'jobPostings' => $jobPostings,
            'activeJobPostings' => $activeJobPostings,
            'candidates' => $candidates,
            'candidatePositions' => $candidatePositions,
            'interviews' => $interviews,
            'interviewCandidates' => $interviewCandidates,
            'employees' => $employees,
        ]);
    }

    // ===== JOB POSTINGS =====
    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|gte:salary_min|min:0',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:active,closed,filled,Đang tuyển,Đã đóng,Đã tuyển đủ',
        ]);

        $statusMap = [
            'Đang tuyển' => 'active',
            'Đã đóng' => 'closed',
            'Đã tuyển đủ' => 'filled',
        ];
        $validated['status'] = $statusMap[$validated['status']] ?? $validated['status'];
        if ($this->hasIsDeletedColumn()) {
            $validated['is_deleted'] = false;
        }

        JobPosting::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Thêm tin tuyển dụng thành công');
    }

    public function updateJob(Request $request, $jobId)
    {
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();

        if ($this->hasIsDeletedColumn() && $job->isDeleted()) {
            return redirect()->route('recruitment.index')->withErrors([
                'job' => 'Tin tuyển dụng đã xóa chỉ có thể xem, không thể chỉnh sửa.',
            ]);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'salary_min' => 'sometimes|required|integer|min:0',
            'salary_max' => 'sometimes|required|integer|min:0',
            'quantity' => 'sometimes|required|integer',
            'description' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|string',
            'deadline' => 'sometimes|required|date',
            'status' => 'required|in:active,closed,filled,Đang tuyển,Đã đóng,Đã tuyển đủ',
        ]);

        $statusMap = [
            'Đang tuyển' => 'active',
            'Đã đóng' => 'closed',
            'Đã tuyển đủ' => 'filled',
        ];
        $salaryMin = $validated['salary_min'] ?? $job->salary_min;
        $salaryMax = $validated['salary_max'] ?? $job->salary_max;

        if ($salaryMax < $salaryMin) {
            return back()->withErrors([
                'salary_max' => 'Mức lương đến phải lớn hơn hoặc bằng mức lương từ.',
            ])->withInput();
        }

        $job->update([
            'title' => $validated['title'] ?? $job->title,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'quantity' => $validated['quantity'] ?? $job->quantity,
            'description' => $validated['description'] ?? $job->description,
            'requirements' => $validated['requirements'] ?? $job->requirements,
            'deadline' => $validated['deadline'] ?? $job->deadline,
            'status' => $statusMap[$validated['status']] ?? $validated['status'],
        ]);

        return redirect()->route('recruitment.index')->with('success', 'Cập nhật tin tuyển dụng thành công');
    }

    public function destroyJob($jobId)
    {
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();

        if ($this->hasIsDeletedColumn() && $job->isDeleted()) {
            return redirect()->route('recruitment.index')->with(
                'success',
                'Tin tuyển dụng này đã ở trạng thái đã xóa.'
            );
        }

        $candidates = Candidate::query()
            ->with('user')
            ->where(function ($query) use ($job) {
                $query->where('job_id', $job->job_id)
                    ->orWhere(function ($legacyQuery) use ($job) {
                        $legacyQuery->whereNull('job_id')
                            ->where('position_applied', $job->title);
                    });
            })
            ->get();

        $candidateUserIds = $candidates->pluck('user_id');

        foreach ($candidates as $candidate) {
            if (empty($candidate->job_id)) {
                $candidate->update(['job_id' => $job->job_id]);
            }

            if ($candidate->user) {
                $candidate->user->notify(new JobPostingDeletedNotification($job));
            }
        }

        $relatedCandidates = $candidateUserIds->count();
        $relatedInterviews = $relatedCandidates > 0
            ? Interview::whereIn('user_id', $candidateUserIds)->count()
            : 0;

        if ($relatedCandidates > 0) {
            $interviews = Interview::whereIn('user_id', $candidateUserIds)->get();
            foreach ($interviews as $interview) {
                $existingNotes = (string) ($interview->notes ?? '');
                if (str_contains($existingNotes, self::JOB_DELETED_NOTE_PREFIX)) {
                    continue;
                }

                $deletedNote = self::JOB_DELETED_NOTE_PREFIX . ' Job "' . $job->title . '" đã bị xóa.';
                $interview->update([
                    'notes' => trim($existingNotes === '' ? $deletedNote : ($existingNotes . "\n" . $deletedNote)),
                ]);
            }
        }

        $jobUpdatePayload = ['status' => 'closed'];
        if ($this->hasIsDeletedColumn()) {
            $jobUpdatePayload['is_deleted'] = true;
        }

        $job->update($jobUpdatePayload);

        return redirect()->route('recruitment.index')->with(
            'success',
            "Đã chuyển tin tuyển dụng sang trạng thái Đã xóa. Giữ lại {$relatedCandidates} ứng viên và {$relatedInterviews} lịch phỏng vấn liên quan."
        );
    }

    // ===== CANDIDATES =====
    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'job_id' => [
                'nullable',
                Rule::exists('job_postings', 'job_id')->where(function ($query) {
                    $query->where('status', 'active');
                    if ($this->hasIsDeletedColumn()) {
                        $query->where('is_deleted', false);
                    }
                }),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Đã nhận việc,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'nullable|date',
        ]);

        $statusMap = [
            'Đậu' => 'Đã nhận việc',
            'Rớt' => 'Từ chối',
            'Nhận việc' => 'Đã nhận việc',
        ];
        $candidateStatus = $statusMap[$validated['status']] ?? $validated['status'];

        // Xử lý upload CV
        $cvPath = null;
        if ($request->hasFile('cv_path')) {
            $cvPath = $this->storeCandidateCv($request->file('cv_path'), $validated['name']);
        }

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            $user = User::create([
                'user_id' => $this->generateNextUserId('US'),
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

        $noteText = $cvPath ? 'CV: ' . $cvPath : null;

        Candidate::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'job_id' => $validated['job_id'] ?? null,
                'position_applied' => $validated['position'],
                'status' => $candidateStatus,
                'applied_date' => $validated['applied_date'] ?? now()->toDateString(),
                'notes' => $noteText,
            ]
        );

        $this->ensureInterviewRecordForCandidate($user->user_id, $candidateStatus);

        return redirect()->route('recruitment.index')->with('success', 'Thêm ứng viên thành công');
    }

    public function updateCandidate(Request $request, $candidateId)
    {
        $candidate = Candidate::where('user_id', $candidateId)->firstOrFail();

        $validated = $request->validate([
            'job_id' => [
                'nullable',
                Rule::exists('job_postings', 'job_id')->where(function ($query) {
                    $query->where('status', 'active');
                    if ($this->hasIsDeletedColumn()) {
                        $query->where('is_deleted', false);
                    }
                }),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Đã nhận việc,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'nullable|date',
        ]);

        $statusMap = [
            'Đậu' => 'Đã nhận việc',
            'Rớt' => 'Từ chối',
            'Nhận việc' => 'Đã nhận việc',
        ];
        $candidateStatus = $statusMap[$validated['status']] ?? $validated['status'];

        // Xử lý upload CV
        $cvPath = null;
        if ($request->hasFile('cv_path')) {
            $cvPath = $this->storeCandidateCv($request->file('cv_path'), $validated['name']);
        }

        $candidate->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $candidate->update([
            'job_id' => $validated['job_id'] ?? null,
            'position_applied' => $validated['position'],
            'status' => $candidateStatus,
            'applied_date' => $validated['applied_date'] ?? $candidate->applied_date,
            'notes' => $cvPath ? 'CV: ' . $cvPath : $candidate->notes,
        ]);

        $this->ensureInterviewRecordForCandidate($candidate->user_id, $candidateStatus);

        return redirect()->route('recruitment.index')->with('success', 'Cập nhật ứng viên thành công');
    }

    public function destroyCandidate($candidateId)
    {
        $candidate = Candidate::where('user_id', $candidateId)->firstOrFail();
        $candidate->delete();

        return redirect()->route('recruitment.index')->with('success', 'Xóa ứng viên thành công');
    }

    // ===== INTERVIEWS =====
    public function storeInterview(Request $request)
    {
        $validated = $request->validate([
            'candidate_email' => 'required|email|exists:users,email',
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'result' => 'required|in:Đã nhận việc,Nhận việc,Từ chối,Đậu,Rớt,Chờ kết quả,pass,fail,pending',
            'notes' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['candidate_email'])->firstOrFail();
        $candidate = Candidate::where('user_id', $user->user_id)->firstOrFail();

        $resultMap = [
            'Đã nhận việc' => 'pass',
            'Nhận việc' => 'pass',
            'Từ chối' => 'fail',
            'Đậu' => 'pass',
            'Rớt' => 'fail',
            'Chờ kết quả' => 'pending',
        ];

        $result = $resultMap[$validated['result']] ?? $validated['result'];

        Interview::create([
            'user_id' => $user->user_id,
            'scheduled_at' => $validated['interview_date'] . ' ' . $validated['interview_time'] . ':00',
            'result' => $result,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update candidate status based on interview result
        if ($result === 'pass') {
            $candidate->update(['status' => 'Đã nhận việc']);
        } elseif ($result === 'fail') {
            $candidate->update(['status' => 'Từ chối']);
        }

        return redirect()->route('recruitment.index')->with('success', 'Thêm lịch phỏng vấn thành công');
    }

    public function updateInterview(Request $request, $interviewId)
    {
        $interview = Interview::where('interview_id', $interviewId)->firstOrFail();

        $validated = $request->validate([
            'candidate_email' => 'required|email|exists:users,email',
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'result' => 'required|in:Đã nhận việc,Nhận việc,Từ chối,Đậu,Rớt,Chờ kết quả,pass,fail,pending',
            'notes' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['candidate_email'])->firstOrFail();
        $candidate = Candidate::where('user_id', $user->user_id)->firstOrFail();

        $resultMap = [
            'Đã nhận việc' => 'pass',
            'Nhận việc' => 'pass',
            'Từ chối' => 'fail',
            'Đậu' => 'pass',
            'Rớt' => 'fail',
            'Chờ kết quả' => 'pending',
        ];

        $result = $resultMap[$validated['result']] ?? $validated['result'];

        $interview->update([
            'user_id' => $user->user_id,
            'scheduled_at' => $validated['interview_date'] . ' ' . $validated['interview_time'] . ':00',
            'result' => $result,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update candidate status based on interview result
        if ($result === 'pass') {
            $candidate->update(['status' => 'Đã nhận việc']);
        } elseif ($result === 'fail') {
            $candidate->update(['status' => 'Từ chối']);
        } else {
            // Keep status as is for pending result
            $candidate->update(['status' => 'Phỏng vấn']);
        }

        return redirect()->route('recruitment.index')->with('success', 'Cập nhật lịch phỏng vấn thành công');
    }

    public function destroyInterview($interviewId)
    {
        $interview = Interview::where('interview_id', $interviewId)->firstOrFail();
        $interview->delete();

        return redirect()->route('recruitment.index')->with('success', 'Xóa lịch phỏng vấn thành công');
    }

    public function submitApplication(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:job_postings,job_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'cv_path' => 'required|file|max:5120',
        ]);

        // Xử lý upload CV
        if ($request->hasFile('cv_path')) {
            $validated['cv_path'] = $this->storeCandidateCv($request->file('cv_path'), $validated['name']);
        }
        $validated['status'] = 'Đang chờ';
        $validated['applied_date'] = now();

        Candidate::create($validated);

        return redirect()->back()->with('success', 'Nộp hồ sơ thành công');
    }

    private function generateNextUserId(string $prefix): string
    {
        do {
            $candidateId = $prefix . '_' . (string) Str::ulid();
        } while (User::where('user_id', $candidateId)->exists());

        return $candidateId;
    }

    private function ensureInterviewRecordForCandidate(string $userId, string $status): void
    {
        if ($status !== 'Phỏng vấn') {
            return;
        }

        $hasInterview = Interview::where('user_id', $userId)->exists();
        if (!$hasInterview) {
            Interview::create([
                'user_id' => $userId,
                'scheduled_at' => null,
                'result' => 'pending',
                'notes' => 'Tạo tự động khi ứng viên chuyển sang trạng thái Phỏng vấn',
            ]);
        }
    }

    private function storeCandidateCv($uploadedFile, string $candidateName): string
    {
        $directory = 'candidates/AD';
        $nameSlug = Str::slug($candidateName, '_');
        $nameSlug = $nameSlug !== '' ? $nameSlug : 'ung_vien';
        $baseName = 'CV_UV_' . $nameSlug;
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: 'pdf');

        $fileName = $baseName . '.' . $extension;
        $counter = 1;

        while (Storage::disk('public')->exists($directory . '/' . $fileName)) {
            $counter++;
            $fileName = $baseName . '_' . $counter . '.' . $extension;
        }

        return $uploadedFile->storeAs($directory, $fileName, 'public');
    }
}
