<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Employee;

class RecruitmentController extends Controller
{
    public function index()
    {
        $jobPostings = JobPosting::paginate(10);
        $candidates = Candidate::with('jobPosting')->paginate(10);
        $interviews = Interview::with(['candidate', 'jobPosting', 'interviewer'])->paginate(10);
        $employees = Employee::where('status', 'Đang làm')->get();

        return view('recruitment', [
            'jobPostings' => $jobPostings,
            'candidates' => $candidates,
            'interviews' => $interviews,
            'employees' => $employees,
        ]);
    }

    // ===== JOB POSTINGS =====
    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'salary_min' => 'required|integer',
            'salary_max' => 'required|integer',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:Đang tuyển,Đã đóng',
        ]);

        JobPosting::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Thêm tin tuyển dụng thành công');
    }

    public function updateJob(Request $request, $jobId)
    {
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'salary_min' => 'required|integer',
            'salary_max' => 'required|integer',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:Đang tuyển,Đã đóng',
        ]);

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
            'job_id' => 'required|exists:job_postings,job_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'required|date',
        ]);

        // Xử lý upload CV
        if ($request->hasFile('cv_path')) {
            $validated['cv_path'] = $request->file('cv_path')->store('cvs', 'public');
        }

        Candidate::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Thêm ứng viên thành công');
    }

    public function updateCandidate(Request $request, $candidateId)
    {
        $candidate = Candidate::where('candidate_id', $candidateId)->firstOrFail();

        $validated = $request->validate([
            'job_id' => 'required|exists:job_postings,job_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:100',
            'status' => 'required|in:Đang chờ,Đã duyệt CV,Phỏng vấn,Nhận việc,Từ chối',
            'cv_path' => 'nullable|file|max:5120',
            'applied_date' => 'required|date',
        ]);

        // Xử lý upload CV
        if ($request->hasFile('cv_path')) {
            $validated['cv_path'] = $request->file('cv_path')->store('cvs', 'public');
        }

        $candidate->update($validated);

        return redirect()->route('recruitment.index')->with('success', 'Cập nhật ứng viên thành công');
    }

    public function destroyCandidate($candidateId)
    {
        $candidate = Candidate::where('candidate_id', $candidateId)->firstOrFail();
        $candidate->delete();

        return redirect()->route('recruitment.index')->with('success', 'Xóa ứng viên thành công');
    }

    // ===== INTERVIEWS =====
    public function storeInterview(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'job_id' => 'required|exists:job_postings,job_id',
            'employee_id' => 'required|exists:employees,employee_id',
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'result' => 'required|in:Đậu,Rớt,Chờ kết quả',
            'notes' => 'nullable|string',
        ]);

        Interview::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Thêm lịch phỏng vấn thành công');
    }

    public function updateInterview(Request $request, $interviewId)
    {
        $interview = Interview::where('interview_id', $interviewId)->firstOrFail();

        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'job_id' => 'required|exists:job_postings,job_id',
            'employee_id' => 'required|exists:employees,employee_id',
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'result' => 'required|in:Đậu,Rớt,Chờ kết quả',
            'notes' => 'nullable|string',
        ]);

        $interview->update($validated);

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
}
