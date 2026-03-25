<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;

class StaffController extends Controller
{
    // Generate user_id with ST_ prefix
    private function generateUserId()
    {
        $lastEmployee = Employee::orderBy('user_id', 'desc')->first();
        $lastNum = $lastEmployee ? (int)substr($lastEmployee->user_id, 3) : 0;
        return 'ST_' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $employees = Employee::with('user', 'department')->paginate(15);
        $departments = Department::all();

        // Pre-check which files exist for each employee
        foreach ($employees as $emp) {
            $emp->cv_file = null;
            $emp->contract_file = null;
            
            $files = Storage::disk('public')->files("employees/{$emp->user_id}");
            
            foreach ($files as $file) {
                if (preg_match('/cv\./i', $file)) {
                    $emp->cv_file = '/storage/' . $file;
                }
                if (preg_match('/contract\./i', $file)) {
                    $emp->contract_file = '/storage/' . $file;
                }
            }
        }

        return view('staff', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }

    public function show($userId)
    {
        $employee = Employee::with('user', 'department')
            ->where('user_id', $userId)
            ->firstOrFail();

        // Return JSON if requested
        $wantJson = request()->wantsJson() 
            || request()->has('json')
            || request()->query('json') === 'true'
            || request()->header('Accept') === 'application/json';
            
        if ($wantJson) {
            return response()->json($employee);
        }

        return view('staff.show', [
            'employee' => $employee,
        ]);
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
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'hometown' => 'nullable|string',
            'current_address' => 'nullable|string',
            'start_date' => 'nullable|date',
            'status' => 'required|string|in:Đang làm,Tạm nghỉ,Nghỉ việc',
            'ethnicity' => 'nullable|string',
            'religion' => 'nullable|string',
            'nationality' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|max:5120',
            'contract_path' => 'nullable|file|max:5120',
        ]);

        // Generate user_id
        $userId = $this->generateUserId();

        // Create User
        $user = User::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'staff',
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ]);

        // Process file uploads
        $employeeData = [
            'user_id' => $userId,
            'position' => $validated['position'],
            'identity_card' => $validated['identity_card'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'hometown' => $validated['hometown'] ?? null,
            'current_address' => $validated['current_address'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'department_id' => $validated['department_id'],
            'ethnicity' => $validated['ethnicity'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $employeeData['avatar_path'] = $request->file('avatar')->store("employees/{$userId}", 'public');
        }
        if ($request->hasFile('cv_path')) {
            $employeeData['cv_path'] = $request->file('cv_path')->store("employees/{$userId}", 'public');
        }
        if ($request->hasFile('contract_path')) {
            $employeeData['contract_path'] = $request->file('contract_path')->store("employees/{$userId}", 'public');
        }

        Employee::create($employeeData);

        return redirect()->route('staff.index')->with('success', 'Thêm nhân viên thành công');
    }

    public function edit($userId)
    {
        $employee = Employee::with('user')
            ->where('user_id', $userId)
            ->firstOrFail();
        $departments = Department::all();

        return view('staff.edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, $userId)
    {
        $employee = Employee::with('user')
            ->where('user_id', $userId)
            ->firstOrFail();
        $user = $employee->user;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'hometown' => 'nullable|string',
            'current_address' => 'nullable|string',
            'start_date' => 'nullable|date',
            'status' => 'required|string|in:Đang làm,Tạm nghỉ,Nghỉ việc',
            'ethnicity' => 'nullable|string',
            'religion' => 'nullable|string',
            'nationality' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|max:5120',
            'contract_path' => 'nullable|file|max:5120',
        ]);

        // Update User
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ]);

        // Prepare Employee data
        $employeeData = [
            'position' => $validated['position'],
            'identity_card' => $validated['identity_card'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'hometown' => $validated['hometown'] ?? null,
            'current_address' => $validated['current_address'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'department_id' => $validated['department_id'],
            'ethnicity' => $validated['ethnicity'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        // Process file uploads
        if ($request->hasFile('avatar')) {
            $employeeData['avatar_path'] = $request->file('avatar')->store("employees/{$userId}", 'public');
        }
        if ($request->hasFile('cv_path')) {
            $employeeData['cv_path'] = $request->file('cv_path')->store("employees/{$userId}", 'public');
        }
        if ($request->hasFile('contract_path')) {
            $employeeData['contract_path'] = $request->file('contract_path')->store("employees/{$userId}", 'public');
        }

        $employee->update($employeeData);

        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công');
    }

    public function destroy($userId)
    {
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $user = $employee->user;

        // Delete Employee first (due to FK constraint)
        $employee->delete();
        // Then delete User
        $user->delete();

        return redirect()->route('staff.index')->with('success', 'Xóa nhân viên thành công');
    }
}

