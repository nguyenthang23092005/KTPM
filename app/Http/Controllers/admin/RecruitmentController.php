<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RecruitmentController extends Controller
{
    public function index()
    {
        $jobPostings = JobPosting::paginate(10);
        $activeJobPostings = JobPosting::where('status', 'active')->orderBy('title')->get();
        $candidates = Candidate::with(['user', 'job'])
            ->where(function ($query) {
                $query->whereNull('job_id')
                    ->orWhereHas('job', function ($jobQuery) {
                        $jobQuery->where('status', 'active');
                    });
            })
            ->paginate(10);
        $interviews = Interview::with(['candidate', 'job', 'interviewer'])->paginate(10);
        $interviewCandidates = Candidate::with('user')
            ->where('status', 'Phỏng vấn')
            ->get();
        $employees = Employee::where('status', 'Đang làm')->get();

        return view('recruitment', [
            'jobPostings' => $jobPostings,
            'activeJobPostings' => $activeJobPostings,
            'candidates' => $candidates,
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
            'deadline' => 'required|date|after_or_equal:today',
            'status' => 'required|in:active,closed,filled,Đang tuyển,Đã đóng,Đã tuyển đủ',
        ]);

        $statusMap = [
            'Đang tuyển' => 'active',
            'Đã đóng' => 'closed',
            'Đã tuyển đủ' => 'filled',
        ];
        $validated['status'] = $statusMap[$validated['status']] ?? $validated['status'];

        JobPosting::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Thêm tin tuyển dụng thành công');
    }

    public function updateJob(Request $request, $jobId)
    {
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|gte:salary_min|min:0',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline' => 'required|date|after_or_equal:today',
            'status' => 'required|in:active,closed,filled,Đang tuyển,Đã đóng,Đã tuyển đủ',
        ]);

        $statusMap = [
            'Đang tuyển' => 'active',
            'Đã đóng' => 'closed',
            'Đã tuyển đủ' => 'filled',
        ];
        $validated['status'] = $statusMap[$validated['status']] ?? $validated['status'];

        $job->update($validated);

        return redirect()->route('recruitment.index')->with('success', 'Cập nhật tin tuyển dụng thành công');
    }

    public function destroyJob($jobId)
    {
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();
        $job->delete();

        return redirect()->route('recruitment.index')->with('success', 'Xóa tin tuyển dụng thành công');
    }

    // ===== CANDIDATES =====
    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'job_id' => [
                'nullable',
                Rule::exists('job_postings', 'job_id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'nullable|date',
        ]);

        $statusMap = [
            'Nhận việc' => 'Đậu',
            'Từ chối' => 'Rớt',
        ];
        $candidateStatus = $statusMap[$validated['status']] ?? $validated['status'];

        // Xử lý upload CV
        $cvPath = null;
        if ($request->hasFile('cv_path')) {
            $cvPath = $request->file('cv_path')->store('cvs', 'public');
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
                Rule::exists('job_postings', 'job_id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'nullable|date',
        ]);

        $statusMap = [
            'Nhận việc' => 'Đậu',
            'Từ chối' => 'Rớt',
        ];
        $candidateStatus = $statusMap[$validated['status']] ?? $validated['status'];

        // Xử lý upload CV
        $cvPath = null;
        if ($request->hasFile('cv_path')) {
            $cvPath = $request->file('cv_path')->store('cvs', 'public');
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
            'result' => 'required|in:Đậu,Rớt,Chờ kết quả,pass,fail,pending',
            'notes' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['candidate_email'])->firstOrFail();
        $candidate = Candidate::where('user_id', $user->user_id)->firstOrFail();

        $resultMap = [
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
            $candidate->update(['status' => 'Đậu']);
        } elseif ($result === 'fail') {
            $candidate->update(['status' => 'Rớt']);
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
            'result' => 'required|in:Đậu,Rớt,Chờ kết quả,pass,fail,pending',
            'notes' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['candidate_email'])->firstOrFail();
        $candidate = Candidate::where('user_id', $user->user_id)->firstOrFail();

        $resultMap = [
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
            $candidate->update(['status' => 'Đậu']);
        } elseif ($result === 'fail') {
            $candidate->update(['status' => 'Rớt']);
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
            $validated['cv_path'] = $request->file('cv_path')->store('cvs', 'public');
        }
        $validated['status'] = 'Đang chờ';
        $validated['applied_date'] = now();

        Candidate::create($validated);

        return redirect()->back()->with('success', 'Nộp hồ sơ thành công');
    }

    private function generateNextUserId(string $prefix): string
    {
        $latest = User::where('user_id', 'like', $prefix . '_%')
            ->orderBy('user_id', 'desc')
            ->value('user_id');

        $lastNumber = 0;
        if ($latest && preg_match('/^' . preg_quote($prefix, '/') . '_(\d+)$/', $latest, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return sprintf('%s_%03d', $prefix, $lastNumber + 1);
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
}
