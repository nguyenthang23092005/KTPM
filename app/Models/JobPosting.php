<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $table = 'job_postings';
    protected $primaryKey = 'job_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($job) {
            if (!$job->job_id) {
                $lastJob = static::orderBy('job_id', 'desc')->first();
                $lastNumber = 0;
                if ($lastJob && preg_match('/JOB_(\d+)/', $lastJob->job_id, $matches)) {
                    $lastNumber = intval($matches[1]);
                }
                $job->job_id = 'JOB_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'job_id', 'title', 'salary_min', 'salary_max', 'quantity',
        'description', 'requirements', 'deadline', 'status'
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'job_id', 'job_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'job_id', 'job_id');
    }
}
