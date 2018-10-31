<?php

namespace App;

use App\Enum\Permission;
use App\Traits\HasParentModel;

class Admin extends User
{
	use HasParentModel;

	public static $role = 'admin';

	public static $permissions = [
		Permission::SEED_ADMIN, 
		Permission::SEED_OPERATOR,
		Permission::SEED_STAFF,
		Permission::SEED_WORKER,
		Permission::SEED_SUBSCRIBER,
	];
}