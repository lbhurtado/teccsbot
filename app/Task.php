<?php

namespace App;

use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
	use SoftDeletes;
    use HasStatuses;

	protected $fillable = [
		'title',
        'instructions',
        'priority',
        'rank',
	];

    protected $dates = [
        'created_at', 
        'updated_at', 
        'accepted_at',
        'started_at',
        'abandoned_at',
        'completed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at')->where('accepted_at', '<=', now());
    }

    public function scopeStarted($query)
    {
        return $query->whereNotNull('started_at')->where('started_at', '<=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at')->where('completed_at', '<=', now());
    }

    public function scopeAbandoned($query)
    {
        return $query->whereNotNull('abandoned_at')->where('abandoned_at', '<=', now());        
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('completed_at')->whereNull('abandoned_at');
    }

    public function isAccepted()
    {
        return ! empty($this->accepted_at); 
    }

    public function isStarted()
    {
        return ! empty($this->started_at); 
    }

    public function hasStarted()
    {
        return $this->isStarted();
    }

    public function isCompleted()
    {
        return ! empty($this->completed_at); 
    }

    public function hasCompleted()
    {
        return $this->isCompleted(); 
    }

    public function isAbandoned()
    {
        return ! empty($this->abandoned_at); 
    }
}
