<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($employee) {
            if (!$employee->employee_id) {
                $lastEmployee = static::orderBy('employee_id', 'desc')->first();
                $lastNumber = 0;
                if ($lastEmployee && preg_match('/NV(\d+)/', $lastEmployee->employee_id, $matches)) {
                    $lastNumber = intval($matches[1]);
                }
                $employee->employee_id = 'NV' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'employee_id', 'name', 'email', 'phone', 'department_id',
        'position', 'identity_card', 'date_of_birth', 'gender',
        'marital_status', 'hometown', 'current_address', 'start_date',
        'status', 'ethnicity', 'religion', 'nationality', 'notes',
        'avatar', 'cv_path', 'contract_path'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'start_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'employee_id', 'employee_id');
    }
}
