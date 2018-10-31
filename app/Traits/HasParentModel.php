<?php

namespace App\Traits;

use App\Enum\Permission as Permissions;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NoRoleDefined;
use Spatie\Permission\Models\{Role, Permission};
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

            $role = Role::findOrCreate(self::$role, $model->getGuardName());

            if (isset(self::$permissions)) {
                foreach (self::$permissions as $permission) {
                    $role->givePermissionTo(Permission::findOrCreate($permission));
                }                
            }

            $model->assignRole(self::$role);            
        });
    }

    public function getGuardName()
    {
    	return $this->guard_name;
    }
}
