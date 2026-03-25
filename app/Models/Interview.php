<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $table = 'interviews';
    protected $primaryKey = 'interview_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($interview) {
            if (!$interview->interview_id) {
                $lastInterview = static::orderBy('interview_id', 'desc')->first();
                $lastNumber = 0;
                if ($lastInterview && preg_match('/IV_(\d+)/', $lastInterview->interview_id, $matches)) {
                    $lastNumber = intval($matches[1]);
                }
                $interview->interview_id = 'IV_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'interview_id', 'candidate_id', 'job_id', 'scheduled_at',
        'interviewer', 'result', 'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
