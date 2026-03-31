<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class StaffController extends Controller
{
    /**
     * Generate unique user_id with ST prefix
     */
    private function generateUserId(): string
    {
        return DB::transaction(function () {
            $maxNum = DB::table('users')
                ->where('user_id', 'like', 'ST_%')
                ->selectRaw("MAX(CAST(SUBSTRING(user_id, 4) AS UNSIGNED)) as max_num")
                ->lockForUpdate()
                ->value('max_num');

            $next = (int)($maxNum ?? 0) + 1;
            return sprintf('ST_%03d', $next);
        });
    }

    private function buildUploadFileName(string $prefix, string $employeeName, $file): string
    {
        $safeName = Str::of($employeeName)
            ->ascii()
            ->replaceMatches('/\s+/', '_')
            ->replaceMatches('/[^A-Za-z0-9_]/', '')
            ->trim('_')
            ->value();

        $safeName = $safeName !== '' ? $safeName : 'NhanVien';
        $extension = strtolower($file->getClientOriginalExtension());

        return $prefix . '_' . $safeName . '.' . $extension;
    }

    private function storeEmployeeDocument(
        string $userId,
        string $employeeName,
        $file,
        string $prefix,
        ?string $existingPath = null
    ): string {
        $directory = 'employees/' . $userId;
        $disk = Storage::disk('public');

        // Keep one document per type to avoid stale files and UI ambiguity.
        foreach ($disk->files($directory) as $path) {
            if (Str::startsWith(basename($path), $prefix . '_')) {
                $disk->delete($path);
            }
        }

        $fileName = $this->buildUploadFileName($prefix, $employeeName, $file);
        $newPath = $file->storeAs($directory, $fileName, 'public');

        if ($existingPath && $existingPath !== $newPath && $disk->exists($existingPath)) {
            $disk->delete($existingPath);
        }

        return $newPath;
    }

    private function resolveEmployeeFilePath(Employee $employee, string $type): ?string
    {
        $pathMap = [
            'avatar' => $employee->avatar_path,
            'cv' => $employee->cv_path,
            'contract' => $employee->contract_path,
        ];

        $path = $pathMap[$type] ?? null;
        if ($path && Storage::disk('public')->exists($path)) {
            return $path;
        }

        $directory = 'employees/' . $employee->user_id;
        if (!Storage::disk('public')->exists($directory)) {
            return null;
        }

        $files = Storage::disk('public')->files($directory);
        $patternMap = [
            'avatar' => '/(^|\\/)(Avt|Avatar)_.*\\.(jpg|jpeg|png|gif|webp)$/i',
            'cv' => '/(^|\\/)(Cv|CV)_.*\\.(pdf|doc|docx)$/i',
            'contract' => '/(^|\\/)(Ct|HD)_.*\\.(pdf|doc|docx)$/i',
        ];

        foreach ($files as $file) {
            if (preg_match($patternMap[$type], $file)) {
                return $file;
            }
        }

        return null;
    }

    public function departmentOverview()
    {
        $departments = Department::withCount('employees')
            ->get()
            ->map(function ($dept) {
                $activeCount = Employee::where('department_id', $dept->department_id)
                    ->where('status', 'Đang làm')
                    ->count();

                $onLeaveCount = Employee::where('department_id', $dept->department_id)
                    ->where('status', 'Tạm nghỉ')
                    ->count();

                $resignedCount = Employee::where('department_id', $dept->department_id)
                    ->where('status', 'Nghỉ việc')
                    ->count();

                return [
                    'department_id' => $dept->department_id,
                    'name' => $dept->name,
                    'description' => $dept->description,
                    'employees_count' => $dept->employees_count,
                    'active_count' => $activeCount,
                    'on_leave_count' => $onLeaveCount,
                    'resigned_count' => $resignedCount,
                ];
            });

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Đang làm')->count();
        $onLeaveEmployees = Employee::where('status', 'Tạm nghỉ')->count();
        $resignedEmployees = Employee::where('status', 'Nghỉ việc')->count();

        return view('staff_departments', [
            'departments' => $departments,
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'onLeaveEmployees' => $onLeaveEmployees,
            'resignedEmployees' => $resignedEmployees,
        ]);
    }

    public function index(Request $request)
    {
        $departmentId = $request->get('department_id');

        $query = Employee::with('user', 'department')
            ->whereHas('user', function ($query) {
                $query->where('user_id', 'like', 'ST_%')
                    ->orWhere('user_id', 'like', 'AD_%');
            });

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get();
        $departments = Department::all();
        $selectedDepartment = $departmentId
            ? Department::where('department_id', $departmentId)->first()
            : null;

        foreach ($employees as $employee) {
            $cvPath = $this->resolveEmployeeFilePath($employee, 'cv');
            $contractPath = $this->resolveEmployeeFilePath($employee, 'contract');
            $avatarPath = $this->resolveEmployeeFilePath($employee, 'avatar');

            $employee->cv_file = $cvPath
                ? route('staff.file', ['userId' => $employee->user_id, 'type' => 'cv'])
                : null;
            $employee->contract_file = $contractPath
                ? route('staff.file', ['userId' => $employee->user_id, 'type' => 'contract'])
                : null;
            $employee->avatar_file = $avatarPath
                ? route('staff.file', ['userId' => $employee->user_id, 'type' => 'avatar'])
                : null;
        }

        return view('staff', [
            'employees' => $employees,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment,
        ]);
    }

    public function show($userId)
    {
        $employee = Employee::with('user', 'department')->where('user_id', $userId)->firstOrFail();
        $departments = Department::all();

        // Check if request wants JSON
        if (request()->wantsJson()) {
            return response()->json($employee->load('user', 'department'));
        }

        return view('staff.show', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function serveFile(string $userId, string $type)
    {
        $employee = Employee::with('user')->where('user_id', $userId)->firstOrFail();

        $path = $this->resolveEmployeeFilePath($employee, $type);
        if (!$path) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($path));
    }

    public function create()
    {
        $departments = Department::all();
        return view('staff.create', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'address' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20|unique:employees,identity_card',
            'marital_status' => 'nullable|string',
            'hometown' => 'nullable|string|max:100',
            'current_address' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string|in:Đang làm,Tạm nghỉ,Nghỉ việc',
            'ethnicity' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'education_level' => 'nullable|string|max:100',
            'previous_experience' => 'nullable|string|max:2000',
            'notes' => 'nullable|string',
            'avatar_path' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'degree' => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'certificates' => 'nullable|string|max:2000',
            'language_certificates' => 'nullable|string|max:2000',
        ]);

        // Create User first
        $userId = $this->generateUserId();
        
        $user = User::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? ($validated['current_address'] ?? null),
        ]);

        // Handle file uploads
        $employeeData = [
            'user_id' => $userId,
            'department_id' => $validated['department_id'],
            'position' => $validated['position'],
            'identity_card' => $validated['identity_card'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'hometown' => $validated['hometown'] ?? null,
            'current_address' => $validated['current_address'] ?? null,
            'start_date' => $validated['start_date'],
            'status' => $validated['status'],
            'ethnicity' => $validated['ethnicity'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'nationality' => $validated['nationality'] ?? 'Việt Nam',
            'education_level' => $validated['education_level'] ?? null,
            'degree' => $validated['degree'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'certificates' => $validated['certificates'] ?? null,
            'language_certificates' => $validated['language_certificates'] ?? null,
            'previous_experience' => $validated['previous_experience'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('avatar_path')) {
            $avatarFile = $request->file('avatar_path');
            $employeeData['avatar_path'] = $this->storeEmployeeDocument($userId, $validated['name'], $avatarFile, 'Avt');
        } elseif ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $employeeData['avatar_path'] = $this->storeEmployeeDocument($userId, $validated['name'], $avatarFile, 'Avt');
        }
        if ($request->hasFile('cv_path')) {
            $cvFile = $request->file('cv_path');
            $employeeData['cv_path'] = $this->storeEmployeeDocument($userId, $validated['name'], $cvFile, 'Cv');
        }
        if ($request->hasFile('contract_path')) {
            $contractFile = $request->file('contract_path');
            $employeeData['contract_path'] = $this->storeEmployeeDocument($userId, $validated['name'], $contractFile, 'Ct');
        }

        Employee::create($employeeData);

        return redirect()->route('staff.list', [
            'department_id' => $validated['department_id'],
            'selected' => $userId,
        ])->with('success', 'Thêm nhân viên thành công');
    }

    public function edit($userId)
    {
        $employee = Employee::with('user')->where('user_id', $userId)->firstOrFail();
        $departments = Department::all();

        // Check authorization: admin or self
        $currentUser = auth()->user();
        if (!$currentUser || ($currentUser->role !== 'admin' && $currentUser->user_id !== $userId)) {
            abort(403, 'Bạn không có quyền chỉnh sửa hồ sơ này');
        }

        return view('staff.edit', [
            'employee' => $employee,
            'user' => $employee->user,
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, $userId)
    {
        $employee = Employee::with('user')->where('user_id', $userId)->firstOrFail();
        $user = $employee->user;

        // Check authorization: admin or self
        $currentUser = auth()->user();
        if (!$currentUser || ($currentUser->role !== 'admin' && $currentUser->user_id !== $userId)) {
            abort(403, 'Bạn không có quyền chỉnh sửa hồ sơ này');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId . ',user_id',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'address' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20|unique:employees,identity_card,' . $userId . ',user_id',
            'marital_status' => 'nullable|string',
            'hometown' => 'nullable|string|max:100',
            'current_address' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string|in:Đang làm,Tạm nghỉ,Nghỉ việc',
            'ethnicity' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'education_level' => 'nullable|string|max:100',
            'previous_experience' => 'nullable|string|max:2000',
            'notes' => 'nullable|string',
            'avatar_path' => 'nullable|image|max:2048',
            'avatar' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'degree' => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'certificates' => 'nullable|string|max:2000',
            'language_certificates' => 'nullable|string|max:2000',
        ]);

        // Update User
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? ($validated['current_address'] ?? $user->address),
        ]);

        // Update Employee
        $employeeData = [
            'department_id' => $validated['department_id'],
            'position' => $validated['position'],
            'marital_status' => $validated['marital_status'] ?? $employee->marital_status,
            'hometown' => $validated['hometown'] ?? $employee->hometown,
            'current_address' => $validated['current_address'] ?? $employee->current_address,
            'start_date' => $validated['start_date'],
            'status' => $validated['status'],
            'ethnicity' => $validated['ethnicity'] ?? $employee->ethnicity,
            'religion' => $validated['religion'] ?? $employee->religion,
            'nationality' => $validated['nationality'] ?? $employee->nationality,
            'education_level' => $validated['education_level'] ?? $employee->education_level,
            'previous_experience' => $validated['previous_experience'] ?? $employee->previous_experience,
            'notes' => $validated['notes'] ?? $employee->notes,
            'degree' => $validated['degree'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'certificates' => $validated['certificates'] ?? null,
            'language_certificates' => $validated['language_certificates'] ?? null,
        ];

        if (!empty($validated['identity_card'])) {
            $employeeData['identity_card'] = $validated['identity_card'];
        }

        if ($request->hasFile('avatar_path')) {
            $avatarFile = $request->file('avatar_path');
            $employeeData['avatar_path'] = $this->storeEmployeeDocument(
                $userId,
                $validated['name'],
                $avatarFile,
                'Avt',
                $employee->avatar_path
            );
        } elseif ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $employeeData['avatar_path'] = $this->storeEmployeeDocument(
                $userId,
                $validated['name'],
                $avatarFile,
                'Avt',
                $employee->avatar_path
            );
        }
        if ($request->hasFile('cv_path')) {
            $cvFile = $request->file('cv_path');
            $employeeData['cv_path'] = $this->storeEmployeeDocument(
                $userId,
                $validated['name'],
                $cvFile,
                'Cv',
                $employee->cv_path
            );
        }
        if ($request->hasFile('contract_path')) {
            $contractFile = $request->file('contract_path');
            $employeeData['contract_path'] = $this->storeEmployeeDocument(
                $userId,
                $validated['name'],
                $contractFile,
                'Ct',
                $employee->contract_path
            );
        }

        $employee->update($employeeData);

        return redirect()->route('staff.list', [
            'department_id' => $validated['department_id'],
            'selected' => $userId,
        ])->with('success', 'Cập nhật nhân viên thành công');
    }

    public function destroy($userId)
    {
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $user = $employee->user;

        // Delete employee first (foreign key constraint)
        $employee->delete();
        
        // Then delete user
        $user->delete();

        return redirect()->route('staff.index')->with('success', 'Xóa nhân viên thành công');
    }
}
