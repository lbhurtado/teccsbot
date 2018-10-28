<?php

namespace App;

use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'code',
        'type',
		'message',
	];

    // cannot use $user
    protected $model;

    public static function record($attributes, $user = null)
    {
        return tap(static::firstOrNew($attributes), function ($placement) use ($user) {
            if (! is_null($user))
                $placement->user()
                    ->associate($user)
                    ->save();
        });
    }

    public static function activate($code, $attributes = [])
    {
        return optional(self::bearing($code)->first())->wake($attributes);
    }

    public function wake($attributes)
    {
        return  $this->conjure($attributes)
                        ->appendToUpline()
                        ->logAsynchronously()
                        ->getUser();
    }

    protected function upline()
    {
        return User::findOrFail($this->user->id);
    }

    protected function conjure($attributes = [])
    {
        $attributes['password'] = bcrypt(env('DEFAULT_PIN', '1234'));
        
        $this->model = $this->type::firstOrCreate($attributes);

        return $this;
    }

    protected function appendToUpline()
    {
        $this->upline()->appendNode($this->getUser());

        return $this;
    }

    protected function logAsynchronously()
    {
        // event(new PlacementWasRecorded($this->getModel(), $this));

        return $this;
    }

    protected function getUser()
    {
        return $this->model ?? false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtolower($value);
    }

    public function scopeBearing($query, $code)
    {
        return $query->where('code', strtolower($code));
    }

    public function scopeBy($query, $user)
    {
        return $query->where('user_id', $user->id);
    }    
}
