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
}
