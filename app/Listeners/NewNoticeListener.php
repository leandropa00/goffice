<?php

namespace App\Listeners;

use App\Events\NewNoticeEvent;
use App\Notifications\NewNotice;
use Illuminate\Support\Facades\Notification;

class NewNoticeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewNoticeEvent $event
     * @return void
     */
    public function handle(NewNoticeEvent $event)
    {
        Notification::send($event->notifyUser, new NewNotice($event->notice));
    }
}
