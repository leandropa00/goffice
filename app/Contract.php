<?php

namespace App;

use App\Observers\ContractObserver;

class Contract extends BaseModel
{
    protected $dates = [
        'start_date',
        'end_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractObserver::class);

    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes(['active']);
    }

    public function contract_type()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function signature()
    {
        return $this->hasOne(ContractSign::class, 'contract_id');
    }

    public function discussion()
    {
        return $this->hasMany(ContractDiscussion::class);
    }

    public function renew_history()
    {
        return $this->hasMany(ContractRenew::class, 'contract_id');
    }
}
