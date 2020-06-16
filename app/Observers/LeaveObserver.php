<?php

namespace App\Observers;

use App\Events\LeaveEvent;
use App\Leave;

class LeaveObserver
{
    public function created(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding() ) {
            event(new LeaveEvent($leave, 'created'));
        }
    }

    public function updated(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            // Send from ManageLeavesController
            if ($leave->isDirty('status')) {
                event(new LeaveEvent($leave, 'statusUpdated'));
            } else {
                event(new LeaveEvent($leave, 'updated'));
            }
        }
    }
}
