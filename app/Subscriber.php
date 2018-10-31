<?php

namespace App;

use App\Traits\HasParentModel;

use App\Enum\Permission;

class Subscriber extends User
{
	use HasParentModel;

	public static $role = 'subscriber';

	public static $permissions = [
		Permission::SEED_SUBSCRIBER
	];
}