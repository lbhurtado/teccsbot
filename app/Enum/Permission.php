<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Permission extends Enum
{
    const SEED_ADMIN 		= 'seed admin';
    const SEED_OPERATOR 	= 'seed operator';
    const SEED_STAFF 		= 'seed staff';
    const SEED_WORKER 		= 'seed worker';
    const SEED_SUBSCRIBER 	= 'seed subscriber';
}