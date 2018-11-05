<?php

namespace App\Notifications;

use App\User;

class InvitationAccepted extends OnDemand
{
    public function __construct(User $downline)
    {
        $name = $downline->name;

        parent::__construct(trans('invite.accepted', compact('name')));
    }
}
