<?php

namespace App\Events;

use App\Leave;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leave;
    public $status;

    public function __construct(Leave $leave, $status)
    {
        $this->leave = $leave;
        $this->status = $status;
    }

}
