<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidates';
    protected $primaryKey = 'candidate_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($candidate) {
            if (!$candidate->candidate_id) {
                $lastCandidate = static::orderBy('candidate_id', 'desc')->first();
                $lastNumber = 0;
                if ($lastCandidate && preg_match('/CND_(\d+)/', $lastCandidate->candidate_id, $matches)) {
                    $lastNumber = intval($matches[1]);
                }
                $candidate->candidate_id = 'CND_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $fillable = [
        'candidate_id', 'job_id', 'name', 'email', 'phone',
        'position', 'status', 'cv_path', 'applied_date'
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'candidate_id', 'candidate_id');
    }
}
