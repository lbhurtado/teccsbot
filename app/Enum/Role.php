<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Role extends Enum
{
    const ADMIN 	 = 'admin';
    const OPERATOR 	 = 'operator';
    const STAFF 	 = 'staff';
    const WORKER 	 = 'worker';
    const SUBSCRIBER = 'subscriber';
}