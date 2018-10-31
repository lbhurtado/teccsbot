<?php

namespace App;

use App\Enum\Permission;
use App\Traits\HasParentModel;

class Operator extends User
{
	use HasParentModel;

	public static $role = 'operator';

	public static $permissions = [
		Permission::SEED_OPERATOR,
		Permission::SEED_STAFF,
		Permission::SEED_WORKER,
		Permission::SEED_SUBSCRIBER,
	];
}