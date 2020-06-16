<?php

namespace App\Observers;

use App\Events\TicketEvent;
use App\Ticket;
use App\UniversalSearch;

class TicketObserver
{
    public function created(Ticket $ticket)
    {
        if (!isRunningInConsoleOrSeeding()) {
            //send admin notification
            event(new TicketEvent($ticket, 'NewTicket'));
        }
    }

    public function updated(Ticket $ticket)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($ticket->isDirty('agent_id')) {
                event(new TicketEvent($ticket, 'TicketAgent'));
            }    
        }
    }

    public function deleting(Ticket $ticket)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $ticket->id)->where('module_type', 'ticket')->get();
        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }
}
