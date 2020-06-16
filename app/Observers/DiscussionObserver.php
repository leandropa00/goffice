<?php

namespace App\Observers;

use App\Discussion;
use App\Events\NewUserEvent;
use App\User;

class DiscussionObserver
{
    public function saving(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            //
        }
    }
}
