<?php

namespace App\Traits;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NoRoleDefined;
use Tightenco\Parental\HasParentModel as BaseHasParentModel;

trait HasParentModel
{
    use BaseHasParentModel {
        BaseHasParentModel::bootHasParentModel as parentBootHasParentModel;
    }

    public static function bootHasParentModel()
    {
        // static::parentBootHasParentModel();
        static::creating(function ($model) {
            if ($model->parentHasReturnsChildModelsTrait()) {
                $model->forceFill(
                    [$model->getInhertanceColumn() => $model->classToAlias(get_class($model))]
                );
            }
        });
        
        static::created(function ($model) {
        
            if (!isset(self::$role)) {

                throw new NoRoleDefined();
            }
            
            Role::findOrCreate(self::$role, $model->getGuardName());

            $model->assignRole(self::$role);            
        });
    }

    public function getGuardName()
    {
    	return $this->guard_name;
    }
}
