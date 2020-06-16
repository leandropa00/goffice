<?php

namespace App\Observers;

use App\Events\LeadEvent;
use App\Lead;
use App\UniversalSearch;

class LeadObserver
{

    public function created(Lead $lead)
    {
        if (request('agent_id') != '') {
            event(new LeadEvent($lead, $lead->lead_agent, 'LeadAgentAssigned'));
        }
    }

    public function deleted(Lead $lead)
    {
        UniversalSearch::where('searchable_id', $lead->id)->where('module_type', 'lead')->delete();
    }
}
