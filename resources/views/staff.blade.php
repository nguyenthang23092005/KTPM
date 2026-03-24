@extends('admin')
@section('title', 'Staff Management')
@section('content')

<div class="flex gap-6 h-[calc(100vh-10rem)]" style="margin-left: -27px; padding-left: 5px;">
    <!-- Employee List Sidebar -->
    <div class="w-80 bg-white rounded-lg shadow p-4 overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Bộ lọc & Tìm kiếm</h3>
        <select class="w-full p-2 mb-4 border border-gray-300 rounded" id="deptFilter">
            <option value="">Phòng ban</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <select class="w-full p-2 mb-4 border border-gray-300 rounded">
            <option value="">Sắp xếp</option>
            <option>Họ Tên</option>
            <option>Mã nhân viên</option>
            <option>Ngày làm việc</option>
        </select>
        <input type="text" placeholder="Tìm kiếm theo tên/MNV" class="w-full p-2 mb-4 border border-gray-300 rounded">
        <ul class="employee-list space-y-2">
            @forelse($employees as $employee)
            <li onclick="selectEmployee(this, '{{ $employee->employee_id }}')" class="p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50 rounded">
                <div class="flex items-center gap-3">
                    <img src="{{ $employee->avatar ?? 'https://via.placeholder.com/35' }}" alt="avatar" class="w-9 h-9 rounded-full">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">{{ $employee->name }}</div>
                        <small class="text-gray-500">{{ $employee->employee_id }}</small>
                        <span class="badge {{ $employee->status === 'Đang làm' ? 'dang-lam' : ($employee->status === 'Tạm nghỉ' ? 'tam-nghi' : 'nghi-viec') }} ml-2 inline-block">{{ $employee->status }}</span>
                    </div>
                </div>
            </li>
            @empty
            <li class="p-3 text-gray-500 text-center">Chưa có nhân viên</li>
            @endforelse
        </ul>
    </div>

    <!-- Employee Detail Form -->
    <div class="flex-1 bg-white rounded-lg shadow p-6 overflow-y-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Chi tiết nhân viên</h2>
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Thêm mới</button>
                    <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">Chỉnh sửa</button>
                    <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">Xóa</button>
                    @elseif(auth()->check() && auth()->user()->role === 'staff')
                    <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">Chỉnh sửa</button>
                    @endif
                </div>
            </div>
            <div class="flex gap-6">
                <img id="empAvatar" src="https://via.placeholder.com/150x180" alt="ảnh nhân viên" class="w-32 h-44 border border-gray-300 rounded">
                <form id="employeeForm" method="POST" action="" enctype="multipart/form-data" class="flex-1 grid grid-cols-3 gap-4">
                    @csrf
                    <input type="hidden" id="employeeId" name="employee_id" value="">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                        <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên</label>
                        <input type="text" id="employeeIdDisplay" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                        <input type="text" id="deptName" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                        <input type="text" id="position" name="position" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CCCD</label>
                        <input type="text" id="identityCard" name="identity_card" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                        <input type="text" id="gender" name="gender" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tình trạng hôn nhân</label>
                        <input type="text" id="maritalStatus" name="marital_status" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quê quán</label>
                        <input type="text" id="hometown" name="hometown" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SĐT</label>
                        <input type="text" id="phone" name="phone" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ hiện tại</label>
                        <input type="text" id="currentAddress" name="current_address" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày làm việc</label>
                        <input type="date" id="startDate" name="start_date" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <input type="text" id="status" name="status" class="w-full p-2 border border-gray-300 rounded bg-green-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dân tộc</label>
                        <input type="text" id="ethnicity" name="ethnicity" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tôn giáo</label>
                        <input type="text" id="religion" name="religion" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quốc tịch</label>
                        <input type="text" id="nationality" name="nationality" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <input type="text" id="notes" name="notes" class="w-full p-2 border border-gray-300 rounded" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CV</label>
                        <a id="cvLink" href="#" target="_blank" class="w-full p-2 bg-blue-500 text-white rounded hover:bg-blue-600 inline-block text-center">Xem</a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hợp đồng</label>
                        <a id="contractLink" href="#" target="_blank" class="w-full p-2 bg-blue-500 text-white rounded hover:bg-blue-600 inline-block text-center">Xem</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-6">
            <div class="flex gap-2 border-b border-gray-200 mb-4 overflow-x-auto">
                <button class="tab-btn active px-4 py-2 border-b-2 border-blue-500 text-blue-500 font-medium" onclick="openTab(event,'thongtin')">Quản lý tin tuyển dụng</button>
                <button class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="openTab(event,'hopdong')">Quản lý ứng viên</button>
                <button class="tab-btn px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800" onclick="openTab(event,'kinhnghiem')">Quản lý phỏng vấn</button>
            </div>
        </div>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
    document.getElementById(tabName).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('border-blue-500', 'text-blue-500'));
    evt.currentTarget.classList.add('border-blue-500', 'text-blue-500');
}

function selectEmployee(el, employeeId) {
    // Get employee data from the list item
    const name = el.querySelector('.font-semibold').textContent;
    const eId = el.querySelector('small').textContent;
    const statusBadge = el.querySelector('.badge').textContent.trim();
    
    // Store data for later use if needed
    alert('Đã chọn: ' + name);
    
    // In a real implementation, you would fetch employee details via AJAX
    // For now, display basic info
    document.getElementById('employeeId').value = employeeId;
    document.getElementById('name').value = name;
    document.getElementById('employeeIdDisplay').value = eId;
    document.getElementById('status').value = statusBadge;
}

// Populate first employee on load
document.addEventListener('DOMContentLoaded', function() {
    const firstEmployee = document.querySelector('.employee-list .active') || document.querySelector('.employee-list li');
    if (firstEmployee) {
        firstEmployee.click();
    }
});
</script>

@endsection
