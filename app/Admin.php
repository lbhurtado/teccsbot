<?php

namespace App;

use App\Traits\HasParentModel;

class Admin extends User
{
	use HasParentModel;

	public static $role = 'admin';
}