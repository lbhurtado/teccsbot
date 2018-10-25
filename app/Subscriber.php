<?php

namespace App;

use App\Traits\HasParentModel;

class Subscriber extends User
{
	use HasParentModel;

	public static $role = 'subscriber';
}