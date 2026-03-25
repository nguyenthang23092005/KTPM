@extends('admin')
@section('title', 'Recruitment Management')
@section('content')

<div class="flex gap-6 h-[calc(100vh-10rem)]" style="margin-left: -27px; padding-left: 5px;">
    <!-- Sidebar Filters -->
    <div class="w-80 bg-white rounded-lg shadow p-4 overflow-y-auto max-h-[calc(100vh-8rem)]">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Bộ lọc & Tìm kiếm</h3>
        
        <!-- Search Tab 1: Job Postings -->
        <div id="filter-jobs" class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">Tin tuyển dụng</span>
                <button onclick="resetFilters()" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm font-medium transition-colors">Làm mới</button>
            </div>
            <input type="text" id="jobSearch" placeholder="Tìm tin tuyển dụng..." class="w-full p-2 border border-gray-300 rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select id="jobStatusFilter" class="w-full p-2 border border-gray-300 rounded">
                    <option value="">Tất cả</option>
                    <option>Đang tuyển</option>
                    <option>Đã đóng</option>
                    <option>Đã tuyển đủ</option>
                </select>
            </div>
        </div>

        <!-- Search Tab 2: Candidates -->
        <div id="filter-candidates" class="space-y-4 hidden">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">Ứng viên</span>
                <button onclick="resetFilters()" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm font-medium transition-colors">Làm mới</button>
            </div>
            <input id="candidateSearch" type="text" placeholder="Tìm ứng viên..." class="w-full p-2 border border-gray-300 rounded">
            <select id="candidateStatusFilter" class="w-full p-2 border border-gray-300 rounded">
                <option value="">Trạng thái</option>
                <option>Đang chờ</option>
                <option>Đã duyệt CV</option>
                <option>Phỏng vấn</option>
                <option>Đậu</option>
                <option>Rớt</option>
            </select>
            <select id="candidatePositionFilter" class="w-full p-2 border border-gray-300 rounded">
                <option value="">Vị trí</option>
                <option>Lập trình viên</option>
                <option>Thiết kế</option>
                <option>Marketing</option>
            </select>
        </div>

        <!-- Search Tab 3: Interviews -->
        <div id="filter-interviews" class="space-y-4 hidden">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">Phỏng vấn</span>
                <button onclick="resetFilters()" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm font-medium transition-colors">Làm mới</button>
            </div>
            <input id="interviewSearch" type="text" placeholder="Tìm ứng viên..." class="w-full p-2 border border-gray-300 rounded">
            <input id="interviewDateFilter" type="date" class="w-full p-2 border border-gray-300 rounded">
        </div>
    </div>

    <!-- Main Content -->
    <div id="recruitmentMainContent" class="flex-1 bg-white rounded-lg shadow p-6 overflow-y-auto max-h-[calc(100vh-8rem)]">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Tuyển Dụng</h1>
        </div>

        @if(auth()->check())
        <div class="mb-4 rounded border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-700">
            Đang đăng nhập: <span class="font-semibold">{{ auth()->user()->email }}</span>
            - Vai trò: <span class="font-semibold uppercase">{{ auth()->user()->role }}</span>
        </div>
        @endif

        <!-- Tabs -->
        <div class="mb-6">
            <div class="flex gap-2 border-b border-gray-200 mb-6 overflow-x-auto">
                <button data-tab="jobs" data-filter="filter-jobs" class="tab-btn active px-4 py-2 border-b-2 border-blue-500 text-blue-500 font-medium" onclick="switchTab(event, 'jobs', 'filter-jobs')">Quản lý Tin Tuyển Dụng</button>
                <button data-tab="candidates" data-filter="filter-candidates" class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="switchTab(event, 'candidates', 'filter-candidates')">Quản lý Ứng Viên</button>
                <button data-tab="interviews" data-filter="filter-interviews" class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="switchTab(event, 'interviews', 'filter-interviews')">Quản lý Phỏng Vấn</button>
            </div>

            <!-- Tab 1: Job Postings -->
            <div id="jobs" class="tab-content">
                <!-- Jobs Table -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Danh Sách Tin Tuyển Dụng</h3>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <button onclick="addJobPosting()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm</button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="jobsTable" class="w-full text-sm border">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tiêu đề</th>
                                    <th class="px-4 py-2 text-left">Mức Lương</th>
                                    <th class="px-4 py-2 text-left">Số Lượng</th>
                                    <th class="px-4 py-2 text-left">Hạn Nộp</th>
                                    <th class="px-4 py-2 text-left">Trạng thái</th>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <th class="px-4 py-2 text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobPostings as $job)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $job->title }}</td>
                                    <td class="px-4 py-2">{{ number_format($job->salary_min) }}-{{ number_format($job->salary_max) }}đ</td>
                                    <td class="px-4 py-2">{{ $job->quantity }}</td>
                                    <td class="px-4 py-2">{{ $job->deadline?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $job->status === 'active' ? 'bg-green-100 text-green-700' : ($job->status === 'filled' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700') }} rounded">
                                            {{ $job->status === 'active' ? 'Đang tuyển' : ($job->status === 'closed' ? 'Đã đóng' : 'Đã tuyển đủ') }}
                                        </span>
                                    </td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center">
                                        <button
                                            type="button"
                                            data-job-id="{{ $job->job_id }}"
                                            data-title="{{ $job->title }}"
                                            data-salary-min="{{ $job->salary_min }}"
                                            data-salary-max="{{ $job->salary_max }}"
                                            data-quantity="{{ $job->quantity }}"
                                            data-deadline="{{ $job->deadline?->format('Y-m-d') }}"
                                            data-description="{{ $job->description }}"
                                            data-requirements="{{ $job->requirements }}"
                                            data-status="{{ $job->status }}"
                                            onclick="editJobPostingFromButton(this)"
                                            class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600"
                                        >Chỉnh Sửa</button>

                                        <form method="POST" action="{{ route('recruitment.destroyJob', $job->job_id) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa tin tuyển dụng?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">Xóa</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="border-b">
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">Chưa có tin tuyển dụng</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Input -->
                <form
                    id="jobForm"
                    method="POST"
                    action="{{ route('recruitment.storeJob') }}"
                    data-store-action="{{ route('recruitment.storeJob') }}"
                    data-update-action-template="{{ url('/recruitment/job/__JOB_ID__') }}"
                    class="space-y-4 border-t pt-6"
                >
                    @csrf
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Tin Tuyển Dụng</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="hidden" id="job_id" name="job_id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                            <input id="job_title" name="title" type="text" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mức Lương Từ</label>
                            <input id="job_salary_min" name="salary_min" type="number" min="0" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mức Lương Đến</label>
                            <input id="job_salary_max" name="salary_max" type="number" min="0" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số Lượng Tuyển</label>
                            <input id="job_quantity" name="quantity" type="number" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hạn Nộp</label>
                            <input id="job_deadline" name="deadline" type="date" min="{{ now()->toDateString() }}" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select id="job_status" name="status" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="active">Đang tuyển</option>
                                <option value="closed">Đã đóng</option>
                                <option value="filled">Đã tuyển đủ</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô Tả</label>
                            <textarea id="job_description" name="description" class="w-full p-2 border border-gray-300 rounded" rows="4" required></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Yêu Cầu</label>
                            <textarea id="job_requirements" name="requirements" class="w-full p-2 border border-gray-300 rounded" rows="4" required></textarea>
                        </div>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="col-span-2 flex gap-2 justify-end">
                            <button type="button" onclick="cancelJobEdit()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100">Hủy</button>
                            <button id="jobSubmitBtn" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu Tin</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tab 2: Candidates -->
            <div id="candidates" class="tab-content hidden">
                <!-- Candidates Table - Example Data -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Danh Sách Ứng Viên</h3>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <button onclick="addCandidate()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm</button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="candidatesTable" class="w-full text-sm border">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left">Họ Tên</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">SĐT</th>
                                    <th class="px-4 py-2 text-left">Vị trí</th>
                                    <th class="px-4 py-2 text-left">Trạng thái</th>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <th class="px-4 py-2 text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($candidates as $candidate)
                                @php
                                    $cvPath = null;
                                    if (!empty($candidate->notes) && \Illuminate\Support\Str::startsWith($candidate->notes, 'CV: ')) {
                                        $cvPath = trim(substr($candidate->notes, 4));
                                    }
                                    $cvUrl = $cvPath ? asset('storage/' . $cvPath) : null;
                                @endphp
                                <tr class="border-b hover:bg-gray-50 cursor-pointer">
                                    <td class="px-4 py-2">{{ $candidate->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $candidate->user?->email ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $candidate->user?->phone ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $candidate->position_applied ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColors = [
                                                'Đang chờ' => 'bg-yellow-100 text-yellow-700',
                                                'Đã duyệt CV' => 'bg-blue-100 text-blue-700',
                                                'Phỏng vấn' => 'bg-purple-100 text-purple-700',
                                                'Đậu' => 'bg-green-100 text-green-700',
                                                'Rớt' => 'bg-red-100 text-red-700',
                                            ];
                                            $colorClass = $statusColors[$candidate->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="px-2 py-1 {{ $colorClass }} rounded">{{ $candidate->status }}</span>
                                    </td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center text-xs">
                                        <button
                                            type="button"
                                            data-user-id="{{ $candidate->user_id }}"
                                            data-name="{{ $candidate->user?->name }}"
                                            data-email="{{ $candidate->user?->email }}"
                                            data-phone="{{ $candidate->user?->phone }}"
                                            data-position="{{ $candidate->position_applied }}"
                                            data-status="{{ $candidate->status }}"
                                            data-job-id="{{ $candidate->job_id }}"
                                            data-cv-url="{{ $cvUrl }}"
                                            data-cv-name="{{ $cvPath ? basename($cvPath) : '' }}"
                                            onclick="editCandidateFromButton(this)"
                                            class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                        >Chỉnh sửa</button>
                                        <form method="POST" action="{{ route('recruitment.destroyCandidate', $candidate->user_id) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa ứng viên?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Xóa</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="border-b">
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">Chưa có ứng viên</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Input -->
                <form id="candidateForm" method="POST" action="{{ route('recruitment.storeCandidate') }}" enctype="multipart/form-data" class="space-y-4 border-t pt-6">
                    @csrf
                    <input type="hidden" id="candidate_user_id">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Ứng Viên</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí ứng tuyển</label>
                            <select id="candidate_job_id" name="job_id" class="w-full p-2 border border-gray-300 rounded">
                                <option value="">Không gán</option>
                                @foreach($activeJobPostings as $job)
                                <option value="{{ $job->job_id }}">{{ $job->title }} ({{ $job->job_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ Tên</label>
                            <input id="candidate_name" name="name" type="text" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí</label>
                            <input id="candidate_position" name="position" type="text" class="w-full p-2 border border-gray-300 rounded" placeholder="Ví dụ: Lập trình viên" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="candidate_email" name="email" type="email" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SĐT</label>
                            <input id="candidate_phone" name="phone" type="text" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select id="candidate_status" name="status" class="w-full p-2 border border-gray-300 rounded" required>
                                <option>Đang chờ</option>
                                <option>Đã duyệt CV</option>
                                <option>Phỏng vấn</option>
                                <option>Nhận việc</option>
                                <option>Từ chối</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CV</label>
                            <input id="candidate_cv" name="cv_path" type="file" class="w-full p-2 border border-gray-300 rounded">
                            <p id="candidate_existing_cv_wrapper" class="mt-2 text-sm text-gray-600 hidden">
                                CV hiện tại:
                                <a
                                    id="candidate_existing_cv_link"
                                    href="#"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 hover:underline"
                                ></a>
                            </p>
                        </div>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="col-span-2 flex gap-2 justify-end">
                            <button type="button" onclick="resetCandidateForm()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100">Hủy</button>
                            <button id="candidateSubmitBtn" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu Ứng Viên</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tab 3: Interviews -->
            <div id="interviews" class="tab-content hidden">
                <!-- Interviews Table - Example Data -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Danh Sách Lịch Phỏng Vấn</h3>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <button onclick="addInterview()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Thêm</button>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table id="interviewsTable" class="w-full text-sm border">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tên</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">SĐT</th>
                                    <th class="px-4 py-2 text-left">Ngày & Giờ</th>
                                    <th class="px-4 py-2 text-left">Kết Quả</th>
                                    <th class="px-4 py-2 text-left">Ghi Chú</th>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <th class="px-4 py-2 text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($interviews as $interview)
                                <tr class="border-b hover:bg-gray-50 cursor-pointer">
                                    <td class="px-4 py-2">{{ $interview->candidate?->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $interview->candidate?->user?->email ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $interview->candidate?->user?->phone ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $interview->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $interview->result === 'pass' ? 'bg-green-100 text-green-700' : ($interview->result === 'fail' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }} rounded">
                                            {{ $interview->result === 'pass' ? 'Đậu' : ($interview->result === 'fail' ? 'Rớt' : 'Chờ kết quả') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">{{ $interview->notes ?? '-' }}</td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center text-xs">
                                        <button
                                            type="button"
                                            data-interview-id="{{ $interview->interview_id }}"
                                            data-name="{{ $interview->candidate?->user?->name }}"
                                            data-email="{{ $interview->candidate?->user?->email }}"
                                            data-phone="{{ $interview->candidate?->user?->phone }}"
                                            data-date="{{ $interview->scheduled_at?->format('Y-m-d') }}"
                                            data-time="{{ $interview->scheduled_at?->format('H:i') }}"
                                            data-result="{{ $interview->result === 'pass' ? 'Đậu' : ($interview->result === 'fail' ? 'Rớt' : 'Chờ kết quả') }}"
                                            data-notes="{{ $interview->notes }}"
                                            onclick="editInterviewFromButton(this)"
                                            class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                        >Chỉnh sửa</button>
                                        <form method="POST" action="{{ route('recruitment.destroyInterview', $interview->interview_id) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa lịch phỏng vấn?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Xóa</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="border-b">
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">Chưa có lịch phỏng vấn</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Input -->
                <form id="interviewForm" method="POST" action="{{ route('recruitment.storeInterview') }}" class="space-y-4 border-t pt-6">
                    @csrf
                    <input type="hidden" id="interview_id">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Lịch Phỏng Vấn</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Ứng Viên</label>
                            <input id="interview_candidate_name" type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="interview_candidate_email" name="candidate_email" type="email" class="w-full p-2 border border-gray-300 rounded" required list="candidateEmails">
                            <datalist id="candidateEmails">
                                @foreach($interviewCandidates as $candidate)
                                <option
                                    value="{{ $candidate->user?->email }}"
                                    data-name="{{ $candidate->user?->name }}"
                                    data-phone="{{ $candidate->user?->phone }}"
                                >{{ $candidate->user?->name }}</option>
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SĐT</label>
                            <input id="interview_candidate_phone" type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày Phỏng Vấn</label>
                            <input id="interview_date" name="interview_date" type="date" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giờ Phỏng Vấn</label>
                            <input id="interview_time" name="interview_time" type="time" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kết Quả</label>
                            <select id="interview_result" name="result" class="w-full p-2 border border-gray-300 rounded" required>
                                <option>Chờ kết quả</option>
                                <option>Đậu</option>
                                <option>Rớt</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi Chú</label>
                            <textarea id="interview_notes" name="notes" class="w-full p-2 border border-gray-300 rounded" rows="4"></textarea>
                        </div>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="col-span-2 flex gap-2 justify-end">
                            <button type="button" onclick="resetInterviewForm()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100">Hủy</button>
                            <button id="interviewSubmitBtn" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu Lịch Phỏng Vấn</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

const TAB_STORAGE_KEY = 'recruitment_active_tab';
const TAB_SCROLL_STORAGE_KEY = 'recruitment_tab_scroll_positions';
const tabFilterMap = {
    jobs: 'filter-jobs',
    candidates: 'filter-candidates',
    interviews: 'filter-interviews',
};
const recruitmentMainContent = document.getElementById('recruitmentMainContent');

function getCurrentTabName() {
    return document.querySelector('.tab-content:not(.hidden)')?.id ?? 'jobs';
}

function getStoredScrollPositions() {
    try {
        return JSON.parse(localStorage.getItem(TAB_SCROLL_STORAGE_KEY) || '{}');
    } catch (_) {
        return {};
    }
}

function storeScrollPosition(tabName, scrollTop) {
    const positions = getStoredScrollPositions();
    positions[tabName] = Math.max(0, scrollTop);
    localStorage.setItem(TAB_SCROLL_STORAGE_KEY, JSON.stringify(positions));
}

function restoreScrollPosition(tabName) {
    if (!recruitmentMainContent) {
        return;
    }

    const positions = getStoredScrollPositions();
    const targetScroll = Number(positions[tabName] ?? 0);
    recruitmentMainContent.scrollTop = Number.isNaN(targetScroll) ? 0 : targetScroll;
}

function activateTab(tabName, filterId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.querySelectorAll('[id^="filter-"]').forEach(filter => {
        filter.classList.add('hidden');
    });

    const tabElement = document.getElementById(tabName);
    const filterElement = document.getElementById(filterId);
    if (!tabElement || !filterElement) {
        return;
    }

    tabElement.classList.remove('hidden');
    filterElement.classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-500');
        btn.classList.add('border-transparent', 'text-gray-600');
    });

    const activeButton = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('border-blue-500', 'text-blue-500');
        activeButton.classList.remove('border-transparent', 'text-gray-600');
    }
}

function switchTab(evt, tabName, filterId) {
    activateTab(tabName, filterId);
    localStorage.setItem(TAB_STORAGE_KEY, tabName);
    restoreScrollPosition(tabName);
}

document.addEventListener('DOMContentLoaded', () => {
    const requestedTab = new URLSearchParams(window.location.search).get('tab');
    const savedTab = localStorage.getItem(TAB_STORAGE_KEY);
    const targetTab = requestedTab || savedTab || 'jobs';
    const targetFilter = tabFilterMap[targetTab] || tabFilterMap.jobs;

    activateTab(targetTab, targetFilter);
    localStorage.setItem(TAB_STORAGE_KEY, targetTab);
    requestAnimationFrame(() => restoreScrollPosition(targetTab));
    initRecruitmentFilters();
});

if (recruitmentMainContent) {
    recruitmentMainContent.addEventListener('scroll', () => {
        storeScrollPosition(getCurrentTabName(), recruitmentMainContent.scrollTop);
    });

    window.addEventListener('beforeunload', () => {
        storeScrollPosition(getCurrentTabName(), recruitmentMainContent.scrollTop);
    });
}

function resetFilters() {
    const activeFilter = document.querySelector('[id^="filter-"]:not(.hidden)');
    if (!activeFilter) {
        return;
    }

    activeFilter.querySelectorAll('input').forEach((input) => {
        input.value = '';
    });

    activeFilter.querySelectorAll('select').forEach((select) => {
        select.selectedIndex = 0;
    });

    const activeTab = getCurrentTabName();
    if (activeTab === 'jobs') {
        applyJobFilters();
    }
    if (activeTab === 'candidates') {
        applyCandidateFilters();
    }
    if (activeTab === 'interviews') {
        applyInterviewFilters();
    }
}

function normalizeText(value) {
    return (value || '').toString().trim().toLowerCase();
}

function setNoResultRow(table, visibleCount, message) {
    const tbody = table.querySelector('tbody');
    if (!tbody) {
        return;
    }

    let row = tbody.querySelector('.js-no-result-row');
    if (visibleCount > 0) {
        if (row) {
            row.remove();
        }
        return;
    }

    if (!row) {
        row = document.createElement('tr');
        row.className = 'js-no-result-row border-b';
        const cell = document.createElement('td');
        cell.colSpan = table.querySelectorAll('thead th').length;
        cell.className = 'px-4 py-4 text-center text-gray-500';
        cell.textContent = message;
        row.appendChild(cell);
        tbody.appendChild(row);
    }
}

function getTableDataRows(table) {
    return Array.from(table.querySelectorAll('tbody tr')).filter((row) => !row.classList.contains('js-no-result-row'));
}

function applyJobFilters() {
    const table = document.getElementById('jobsTable');
    if (!table) {
        return;
    }

    const keyword = normalizeText(document.getElementById('jobSearch')?.value);
    const status = normalizeText(document.getElementById('jobStatusFilter')?.value);
    const rows = getTableDataRows(table);
    let visibleCount = 0;

    rows.forEach((row) => {
        const title = normalizeText(row.cells[0]?.textContent);
        const statusText = normalizeText(row.cells[4]?.textContent);
        const matchKeyword = !keyword || title.includes(keyword);
        const matchStatus = !status || statusText.includes(status);
        const isVisible = matchKeyword && matchStatus;

        row.style.display = isVisible ? '' : 'none';
        if (isVisible) {
            visibleCount += 1;
        }
    });

    setNoResultRow(table, visibleCount, 'Không có tin tuyển dụng phù hợp bộ lọc');
}

function applyCandidateFilters() {
    const table = document.getElementById('candidatesTable');
    if (!table) {
        return;
    }

    const keyword = normalizeText(document.getElementById('candidateSearch')?.value);
    const status = normalizeText(document.getElementById('candidateStatusFilter')?.value);
    const position = normalizeText(document.getElementById('candidatePositionFilter')?.value);
    const rows = getTableDataRows(table);
    let visibleCount = 0;

    rows.forEach((row) => {
        const name = normalizeText(row.cells[0]?.textContent);
        const email = normalizeText(row.cells[1]?.textContent);
        const phone = normalizeText(row.cells[2]?.textContent);
        const positionText = normalizeText(row.cells[3]?.textContent);
        const statusText = normalizeText(row.cells[4]?.textContent);

        const matchKeyword = !keyword || name.includes(keyword) || email.includes(keyword) || phone.includes(keyword);
        const matchStatus = !status || statusText.includes(status);
        const matchPosition = !position || positionText.includes(position);
        const isVisible = matchKeyword && matchStatus && matchPosition;

        row.style.display = isVisible ? '' : 'none';
        if (isVisible) {
            visibleCount += 1;
        }
    });

    setNoResultRow(table, visibleCount, 'Không có ứng viên phù hợp bộ lọc');
}

function applyInterviewFilters() {
    const table = document.getElementById('interviewsTable');
    if (!table) {
        return;
    }

    const keyword = normalizeText(document.getElementById('interviewSearch')?.value);
    const date = document.getElementById('interviewDateFilter')?.value || '';
    const rows = getTableDataRows(table);
    let visibleCount = 0;

    rows.forEach((row) => {
        const name = normalizeText(row.cells[0]?.textContent);
        const email = normalizeText(row.cells[1]?.textContent);
        const phone = normalizeText(row.cells[2]?.textContent);
        const dateTimeText = normalizeText(row.cells[3]?.textContent);
        const dateText = dateTimeText.slice(0, 10);

        const matchKeyword = !keyword || name.includes(keyword) || email.includes(keyword) || phone.includes(keyword);
        const matchDate = !date || dateText === date;
        const isVisible = matchKeyword && matchDate;

        row.style.display = isVisible ? '' : 'none';
        if (isVisible) {
            visibleCount += 1;
        }
    });

    setNoResultRow(table, visibleCount, 'Không có lịch phỏng vấn phù hợp bộ lọc');
}

function initRecruitmentFilters() {
    const listeners = [
        { id: 'jobSearch', event: 'input', handler: applyJobFilters },
        { id: 'jobStatusFilter', event: 'change', handler: applyJobFilters },
        { id: 'candidateSearch', event: 'input', handler: applyCandidateFilters },
        { id: 'candidateStatusFilter', event: 'change', handler: applyCandidateFilters },
        { id: 'candidatePositionFilter', event: 'change', handler: applyCandidateFilters },
        { id: 'interviewSearch', event: 'input', handler: applyInterviewFilters },
        { id: 'interviewDateFilter', event: 'change', handler: applyInterviewFilters },
    ];

    listeners.forEach(({ id, event, handler }) => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener(event, handler);
        }
    });

    applyJobFilters();
    applyCandidateFilters();
    applyInterviewFilters();
}

// Job Posting actions
function addJobPosting() {
    const form = document.getElementById('jobForm');
    form.action = form.dataset.storeAction;
    document.getElementById('job_id').value = '';
    document.getElementById('job_title').value = '';
    document.getElementById('job_salary_min').value = '';
    document.getElementById('job_salary_max').value = '';
    document.getElementById('job_quantity').value = '';
    document.getElementById('job_deadline').value = '';
    document.getElementById('job_description').value = '';
    document.getElementById('job_requirements').value = '';
    document.getElementById('job_status').value = 'active';
    document.getElementById('jobSubmitBtn').textContent = 'Lưu Tin';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function editJobPosting(job) {
    const form = document.getElementById('jobForm');
    form.action = form.dataset.updateActionTemplate.replace('__JOB_ID__', job.job_id);
    document.getElementById('job_id').value = job.job_id ?? '';
    document.getElementById('job_title').value = job.title ?? '';
    document.getElementById('job_salary_min').value = job.salary_min ?? '';
    document.getElementById('job_salary_max').value = job.salary_max ?? '';
    document.getElementById('job_quantity').value = job.quantity ?? '';
    document.getElementById('job_deadline').value = job.deadline ?? '';
    document.getElementById('job_description').value = job.description ?? '';
    document.getElementById('job_requirements').value = job.requirements ?? '';
    document.getElementById('job_status').value = job.status ?? 'active';
    document.getElementById('jobSubmitBtn').textContent = 'Cập Nhật';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function editJobPostingFromButton(button) {
    const job = {
        job_id: button.dataset.jobId,
        title: button.dataset.title,
        salary_min: button.dataset.salaryMin,
        salary_max: button.dataset.salaryMax,
        quantity: button.dataset.quantity,
        deadline: button.dataset.deadline,
        description: button.dataset.description,
        requirements: button.dataset.requirements,
        status: button.dataset.status,
    };

    editJobPosting(job);
}

function cancelJobEdit() {
    addJobPosting();
}

// Candidate actions
function addCandidate() {
    resetCandidateForm();
    document.getElementById('candidateForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetCandidateForm() {
    const form = document.getElementById('candidateForm');
    form.action = "{{ route('recruitment.storeCandidate') }}";
    document.getElementById('candidate_user_id').value = '';
    document.getElementById('candidate_name').value = '';
    document.getElementById('candidate_position').value = '';
    document.getElementById('candidate_email').value = '';
    document.getElementById('candidate_phone').value = '';
    document.getElementById('candidate_job_id').selectedIndex = 0;
    document.getElementById('candidate_status').selectedIndex = 0;
    document.getElementById('candidate_cv').value = '';
    const existingCvWrapper = document.getElementById('candidate_existing_cv_wrapper');
    const existingCvLink = document.getElementById('candidate_existing_cv_link');
    existingCvLink.href = '#';
    existingCvLink.textContent = '';
    existingCvWrapper.classList.add('hidden');
    document.getElementById('candidateSubmitBtn').textContent = 'Lưu Ứng Viên';
}

function saveCandidate() {
    document.getElementById('candidateForm').submit();
}

function editCandidate(id) {
    const button = document.querySelector(`[data-user-id="${id}"]`);
    if (button) {
        editCandidateFromButton(button);
    }
}

function editCandidateFromButton(button) {
    const form = document.getElementById('candidateForm');
    const statusMap = {
        'Đậu': 'Nhận việc',
        'Rớt': 'Từ chối',
    };
    form.action = `{{ url('/recruitment/candidate') }}/${button.dataset.userId}`;
    document.getElementById('candidate_user_id').value = button.dataset.userId ?? '';
    document.getElementById('candidate_name').value = button.dataset.name ?? '';
    document.getElementById('candidate_email').value = button.dataset.email ?? '';
    document.getElementById('candidate_phone').value = button.dataset.phone ?? '';
    document.getElementById('candidate_job_id').value = button.dataset.jobId ?? '';
    document.getElementById('candidate_position').value = button.dataset.position ?? '';
    document.getElementById('candidate_status').value = statusMap[button.dataset.status] ?? button.dataset.status ?? 'Đang chờ';
    const existingCvWrapper = document.getElementById('candidate_existing_cv_wrapper');
    const existingCvLink = document.getElementById('candidate_existing_cv_link');
    if (button.dataset.cvUrl) {
        existingCvLink.href = button.dataset.cvUrl;
        existingCvLink.textContent = button.dataset.cvName || 'Xem CV đã tải';
        existingCvWrapper.classList.remove('hidden');
    } else {
        existingCvLink.href = '#';
        existingCvLink.textContent = '';
        existingCvWrapper.classList.add('hidden');
    }
    document.getElementById('candidateSubmitBtn').textContent = 'Cập Nhật Ứng Viên';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function deleteCandidate(id) {
    if (confirm('Bạn chắc chắn muốn xóa ứng viên?')) {
        // TODO: Send DELETE request to /recruitment/candidate/{id}
        alert('Xóa ứng viên thành công');
    }
}

// Interview actions
function addInterview() {
    resetInterviewForm();
    document.getElementById('interviewForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetInterviewForm() {
    const form = document.getElementById('interviewForm');
    form.action = "{{ route('recruitment.storeInterview') }}";
    document.getElementById('interview_id').value = '';
    document.getElementById('interview_candidate_name').value = '';
    document.getElementById('interview_candidate_email').value = '';
    document.getElementById('interview_candidate_phone').value = '';
    document.getElementById('interview_date').value = '';
    document.getElementById('interview_time').value = '';
    document.getElementById('interview_result').selectedIndex = 0;
    document.getElementById('interview_notes').value = '';
    document.getElementById('interviewSubmitBtn').textContent = 'Lưu Lịch Phỏng Vấn';
}

function saveInterview() {
    document.getElementById('interviewForm').submit();
}

const interviewEmailInput = document.getElementById('interview_candidate_email');
if (interviewEmailInput) {
    interviewEmailInput.addEventListener('change', function () {
        const option = document.querySelector(`#candidateEmails option[value="${this.value}"]`);
        if (!option) {
            return;
        }

        document.getElementById('interview_candidate_name').value = option.dataset.name ?? '';
        document.getElementById('interview_candidate_phone').value = option.dataset.phone ?? '';
    });
}

function editInterview(id) {
    const button = document.querySelector(`[data-interview-id="${id}"]`);
    if (button) {
        editInterviewFromButton(button);
    }
}

function editInterviewFromButton(button) {
    const form = document.getElementById('interviewForm');
    form.action = `{{ url('/recruitment/interview') }}/${button.dataset.interviewId}`;
    document.getElementById('interview_id').value = button.dataset.interviewId ?? '';
    document.getElementById('interview_candidate_name').value = button.dataset.name ?? '';
    document.getElementById('interview_candidate_email').value = button.dataset.email ?? '';
    document.getElementById('interview_candidate_phone').value = button.dataset.phone ?? '';
    document.getElementById('interview_date').value = button.dataset.date ?? '';
    document.getElementById('interview_time').value = button.dataset.time ?? '';
    document.getElementById('interview_result').value = button.dataset.result ?? 'Chờ kết quả';
    document.getElementById('interview_notes').value = button.dataset.notes ?? '';
    document.getElementById('interviewSubmitBtn').textContent = 'Cập Nhật Lịch Phỏng Vấn';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function deleteInterview(id) {
    if (confirm('Bạn chắc chắn muốn xóa lịch phỏng vấn?')) {
        // Deprecated: delete now handled by form submit in table
    }
}

// Application submission
function submitApplication() {
    alert('Mở form nộp hồ sơ');
    // TODO: Show application form modal
}

function addNewItem() {
    alert('Thêm mục mới');
}

function editItem() {
    alert('Chỉnh sửa mục');
}

function deleteItem() {
    alert('Xóa mục');
}
</script>

@endsection
