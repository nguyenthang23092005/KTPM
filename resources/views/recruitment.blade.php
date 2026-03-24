@extends('admin')
@section('title', 'Recruitment Management')
@section('content')

<div class="flex gap-6 h-[calc(100vh-10rem)]" style="margin-left: -27px; padding-left: 5px;">
    <!-- Sidebar Filters -->
    <div class="w-80 bg-white rounded-lg shadow p-4 overflow-y-auto max-h-[calc(100vh-8rem)]">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Bộ lọc & Tìm kiếm</h3>
        
        <!-- Search Tab 1: Job Postings -->
        <div id="filter-jobs" class="space-y-4">
            <input type="text" id="jobSearch" placeholder="Tìm tin tuyển dụng..." class="w-full p-2 border border-gray-300 rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select class="w-full p-2 border border-gray-300 rounded">
                    <option value="">Tất cả</option>
                    <option>Đang tuyển</option>
                    <option>Đã đóng</option>
                </select>
            </div>
        </div>

        <!-- Search Tab 2: Candidates -->
        <div id="filter-candidates" class="space-y-4 hidden">
            <input type="text" placeholder="Tìm ứng viên..." class="w-full p-2 border border-gray-300 rounded">
            <select class="w-full p-2 border border-gray-300 rounded">
                <option value="">Trạng thái</option>
                <option>Đang chờ</option>
                <option>Đã duyệt CV</option>
                <option>Phỏng vấn</option>
                <option>Đậu</option>
                <option>Rớt</option>
            </select>
            <select class="w-full p-2 border border-gray-300 rounded">
                <option value="">Vị trí</option>
                <option>Lập trình viên</option>
                <option>Thiết kế</option>
                <option>Marketing</option>
            </select>
        </div>

        <!-- Search Tab 3: Interviews -->
        <div id="filter-interviews" class="space-y-4 hidden">
            <input type="text" placeholder="Tìm ứng viên..." class="w-full p-2 border border-gray-300 rounded">
            <input type="date" class="w-full p-2 border border-gray-300 rounded">
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-white rounded-lg shadow p-6 overflow-y-auto max-h-[calc(100vh-8rem)]">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Tuyển Dụng</h1>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="flex gap-2 border-b border-gray-200 mb-6 overflow-x-auto">
                <button class="tab-btn active px-4 py-2 border-b-2 border-blue-500 text-blue-500 font-medium" onclick="switchTab(event, 'jobs', 'filter-jobs')">Quản lý Tin Tuyển Dụng</button>
                <button class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="switchTab(event, 'candidates', 'filter-candidates')">Quản lý Ứng Viên</button>
                <button class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="switchTab(event, 'interviews', 'filter-interviews')">Quản lý Phỏng Vấn</button>
            </div>

            <!-- Tab 1: Job Postings -->
            <div id="jobs" class="tab-content">
                <!-- Jobs Table -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Danh Sách Tin Tuyển Dụng</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border">
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
                                    <td class="px-4 py-2">{{ $job->deadline->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $job->status === 'Đang tuyển' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded">{{ $job->status }}</span>
                                    </td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center">
                                        <button onclick="editJobPosting('{{ $job->job_id }}')" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">Chỉnh Sửa</button>
                                        <button onclick="deleteJobPosting('{{ $job->job_id }}')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">Xóa</button>
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
                <div class="space-y-4 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Tin Tuyển Dụng</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mức Lương</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số Lượng Tuyển</label>
                            <input type="number" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hạn Nộp</label>
                            <input type="date" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô Tả</label>
                            <textarea class="w-full p-2 border border-gray-300 rounded" rows="4"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Yêu Cầu</label>
                            <textarea class="w-full p-2 border border-gray-300 rounded" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Candidates -->
            <div id="candidates" class="tab-content hidden">
                <!-- Candidates Table - Example Data -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Danh Sách Ứng Viên</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border">
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
                                <tr class="border-b hover:bg-gray-50 cursor-pointer">
                                    <td class="px-4 py-2">{{ $candidate->name }}</td>
                                    <td class="px-4 py-2">{{ $candidate->email }}</td>
                                    <td class="px-4 py-2">{{ $candidate->phone }}</td>
                                    <td class="px-4 py-2">{{ $candidate->position }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">{{ $candidate->status }}</span>
                                    </td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center text-xs">
                                        <button onclick="editCandidate('{{ $candidate->candidate_id }}')" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">Edit</button>
                                        <button onclick="deleteCandidate('{{ $candidate->candidate_id }}')" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Del</button>
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
                <div class="space-y-4 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Ứng Viên</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ Tên</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SĐT</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí Ứng Tuyển</label>
                            <select class="w-full p-2 border border-gray-300 rounded">
                                <option>Lập trình viên</option>
                                <option>Thiết kế</option>
                                <option>Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select class="w-full p-2 border border-gray-300 rounded">
                                <option>Đang chờ</option>
                                <option>Đã duyệt CV</option>
                                <option>Phỏng vấn</option>
                                <option>Đậu</option>
                                <option>Rớt</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CV</label>
                            <input type="file" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Interviews -->
            <div id="interviews" class="tab-content hidden">
                <!-- Interviews Table - Example Data -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Danh Sách Lịch Phỏng Vấn</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border">
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
                                    <td class="px-4 py-2">{{ $interview->candidate->name }}</td>
                                    <td class="px-4 py-2">{{ $interview->candidate->email }}</td>
                                    <td class="px-4 py-2">{{ $interview->candidate->phone }}</td>
                                    <td class="px-4 py-2">{{ $interview->interview_date->format('Y-m-d') }} {{ $interview->interview_time }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 {{ $interview->result === 'Đậu' ? 'bg-green-100 text-green-700' : ($interview->result === 'Rớt' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }} rounded">{{ $interview->result }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">{{ $interview->notes ?? '-' }}</td>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="px-4 py-2 flex gap-2 justify-center text-xs">
                                        <button onclick="editInterview('{{ $interview->interview_id }}')" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">Edit</button>
                                        <button onclick="deleteInterview('{{ $interview->interview_id }}')" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Del</button>
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
                <div class="space-y-4 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900">Thêm/Cập nhật Lịch Phỏng Vấn</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Ứng Viên</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SĐT</label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày Phỏng Vấn</label>
                            <input type="date" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giờ Phỏng Vấn</label>
                            <input type="time" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kết Quả</label>
                            <select class="w-full p-2 border border-gray-300 rounded">
                                <option>Chưa phỏng vấn</option>
                                <option>Đậu</option>
                                <option>Rớt</option>
                                <option>Chờ kết quả</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi Chú</label>
                            <textarea class="w-full p-2 border border-gray-300 rounded" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(evt, tabName, filterId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.querySelectorAll('[id^="filter-"]').forEach(filter => {
        filter.classList.add('hidden');
    });
    
    // Show selected tab and filter
    document.getElementById(tabName).classList.remove('hidden');
    document.getElementById(filterId).classList.remove('hidden');
    
    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-500');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    evt.currentTarget.classList.add('border-blue-500', 'text-blue-500');
    evt.currentTarget.classList.remove('border-transparent', 'text-gray-600');
}

// Job Posting actions
function editJobPosting(id) {
    alert('Chỉnh sửa tin tuyển dụng: ' + id);
    // TODO: Open edit form or modal
}

function deleteJobPosting(id) {
    if (confirm('Bạn chắc chắn muốn xóa tin tuyển dụng?')) {
        // TODO: Send DELETE request to /recruitment/job/{id}
        alert('Xóa tin tuyển dụng thành công');
    }
}

// Candidate actions
function editCandidate(id) {
    alert('Chỉnh sửa ứng viên: ' + id);
    // TODO: Open edit form or modal
}

function deleteCandidate(id) {
    if (confirm('Bạn chắc chắn muốn xóa ứng viên?')) {
        // TODO: Send DELETE request to /recruitment/candidate/{id}
        alert('Xóa ứng viên thành công');
    }
}

// Interview actions
function editInterview(id) {
    alert('Chỉnh sửa lịch phỏng vấn: ' + id);
    // TODO: Open edit form or modal
}

function deleteInterview(id) {
    if (confirm('Bạn chắc chắn muốn xóa lịch phỏng vấn?')) {
        // TODO: Send DELETE request to /recruitment/interview/{id}
        alert('Xóa lịch phỏng vấn thành công');
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
