<?php

namespace App;

use App\Traits\HasParentModel;

class Worker extends User
{
	use HasParentModel;

	public static $role = 'worker';
}