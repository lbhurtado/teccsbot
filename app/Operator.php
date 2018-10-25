<?php

namespace App;

use App\Traits\HasParentModel;

class Operator extends User
{
	use HasParentModel;

	public static $role = 'operator';
}