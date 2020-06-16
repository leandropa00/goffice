<?php

namespace App\Observers;

use App\Events\NewPaymentEvent;
use App\Payment;
use App\User;

class PaymentObserver
{

    public function saved(Payment $payment){
        if (!isRunningInConsoleOrSeeding() ) {
            if (($payment->project_id && $payment->project->client_id != null) || ($payment->invoice_id && $payment->invoice->client_id != null)) {
                $clientId = ($payment->project_id && $payment->project->client_id != null) ? $payment->project->client_id : $payment->invoice->client_id;

                // Notify client
                $notifyUser = User::findOrFail($clientId);
                event(new NewPaymentEvent($payment, $notifyUser));
            }
        }
    }

}
