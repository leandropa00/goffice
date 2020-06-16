<?php

namespace App\Observers;

use App\Events\TicketReplyEvent;
use App\TicketReply;
use Illuminate\Support\Facades\Auth;

class TicketReplyObserver
{
    public function created(TicketReply $ticketReply)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (count($ticketReply->ticket->reply) > 1) {
                if (Auth::user()->hasRole('client') && !is_null($ticketReply->ticket->agent)) {
                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->agent));
                } else if (Auth::user()->hasRole('client') && is_null($ticketReply->ticket->agent)) {
                    event(new TicketReplyEvent($ticketReply, null));
                } else {
                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->client));
                }
            }
        }
    }
}
