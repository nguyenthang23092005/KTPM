<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;

class StaffController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->paginate(15);
        $departments = Department::all();

        return view('staff', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }

    public function show($employeeId)
    {
        $employee = Employee::where('employee_id', $employeeId)->firstOrFail();
        $departments = Department::all();

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
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

        // Xử lý upload file
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cv_path')) {
            $validated['cv_path'] = $request->file('cv_path')->store('cvs', 'public');
        }
        if ($request->hasFile('contract_path')) {
            $validated['contract_path'] = $request->file('contract_path')->store('contracts', 'public');
        }

        Employee::create($validated);

        return redirect()->route('staff.index')->with('success', 'Thêm nhân viên thành công');
    }

    public function edit($employeeId)
    {
        $employee = Employee::where('employee_id', $employeeId)->firstOrFail();
        $departments = Department::all();

        return view('staff.edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, $employeeId)
    {
        $employee = Employee::where('employee_id', $employeeId)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->employee_id . ',employee_id',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,department_id',
            'position' => 'required|string|max:100',
            'identity_card' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
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

        // Xử lý upload file
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cv_path')) {
            $validated['cv_path'] = $request->file('cv_path')->store('cvs', 'public');
        }
        if ($request->hasFile('contract_path')) {
            $validated['contract_path'] = $request->file('contract_path')->store('contracts', 'public');
        }

        $employee->update($validated);

        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công');
    }

    public function destroy($employeeId)
    {
        $employee = Employee::where('employee_id', $employeeId)->firstOrFail();
        $employee->delete();

        return redirect()->route('staff.index')->with('success', 'Xóa nhân viên thành công');
    }
}
