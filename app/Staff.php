<?php

namespace App;

use App\Traits\HasParentModel;

class Staff extends User
{
	use HasParentModel;

	public static $role = 'staff';
}