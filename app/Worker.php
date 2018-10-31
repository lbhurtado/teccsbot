<?php

namespace App;

use App\Enum\Permission;
use App\Traits\HasParentModel;

class Worker extends User
{
	use HasParentModel;

	public static $role = 'worker';

	public static $permissions = [
		Permission::SEED_WORKER,
		Permission::SEED_SUBSCRIBER
	];
}