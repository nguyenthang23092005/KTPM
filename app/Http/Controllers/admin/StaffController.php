<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function index()
    {
        $employees = Employee::with('user', 'department')->paginate(15);
        $departments = Department::all();

        return view('staff', [
            'employees' => $employees,
            'departments' => $departments,
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
            'firstName' => 'required|string|max:50',
            'lastName' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'address' => 'required|string|max:255',
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
            'notes' => 'nullable|string',
            'avatar_path' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Create User first
        $userId = $this->generateUserId();
        
        $user = User::create([
            'user_id' => $userId,
            'name' => trim($validated['firstName'] . ' ' . $validated['lastName']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
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
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('avatar_path')) {
            $employeeData['avatar_path'] = $request->file('avatar_path')->store('employees/' . $userId, 'public');
        }
        if ($request->hasFile('cv_path')) {
            $employeeData['cv_path'] = $request->file('cv_path')->store('employees/' . $userId, 'public');
        }
        if ($request->hasFile('contract_path')) {
            $employeeData['contract_path'] = $request->file('contract_path')->store('employees/' . $userId, 'public');
        }

        Employee::create($employeeData);

        return redirect()->route('staff.index')->with('success', 'Thêm nhân viên thành công');
    }

    public function edit($userId)
    {
        $employee = Employee::with('user')->where('user_id', $userId)->firstOrFail();
        $departments = Department::all();

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

        $validated = $request->validate([
            'firstName' => 'required|string|max:50',
            'lastName' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $userId . ',user_id',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'address' => 'required|string|max:255',
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
            'notes' => 'nullable|string',
            'avatar_path' => 'nullable|image|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Update User
        $user->update([
            'name' => trim($validated['firstName'] . ' ' . $validated['lastName']),
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
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
            'notes' => $validated['notes'] ?? $employee->notes,
        ];

        if (!empty($validated['identity_card'])) {
            $employeeData['identity_card'] = $validated['identity_card'];
        }

        if ($request->hasFile('avatar_path')) {
            $employeeData['avatar_path'] = $request->file('avatar_path')->store('employees/' . $userId, 'public');
        }
        if ($request->hasFile('cv_path')) {
            $employeeData['cv_path'] = $request->file('cv_path')->store('employees/' . $userId, 'public');
        }
        if ($request->hasFile('contract_path')) {
            $employeeData['contract_path'] = $request->file('contract_path')->store('employees/' . $userId, 'public');
        }

        $employee->update($employeeData);

        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công');
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
