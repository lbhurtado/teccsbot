<?php

namespace App;

use App\Enum\Permission;
use App\Traits\HasParentModel;

class Staff extends User
{
	use HasParentModel;

	public static $role = 'staff';

	public static $permissions = [
		Permission::SEED_STAFF,
		Permission::SEED_WORKER,
		Permission::SEED_SUBSCRIBER,
	];
}