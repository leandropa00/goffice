<?php

namespace App\Observers;

use App\Estimate;
use App\Events\NewEstimateEvent;
use App\UniversalSearch;

class EstimateObserver
{

    public function created(Estimate $estimate)
    {
        if (!isRunningInConsoleOrSeeding() ) {
            event(new NewEstimateEvent($estimate));
        }
    }

    public function deleting(Estimate $estimate){
        $universalSearches = UniversalSearch::where('searchable_id', $estimate->id)->where('module_type', 'estimate')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
