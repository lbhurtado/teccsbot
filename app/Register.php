<?php

namespace App;

use App\Repositories\Parameterized;

class Register extends Parameterized
{
	protected $regex = "/^(?<code>\S*)\s(?<mobile>\S*)$/i";
}
