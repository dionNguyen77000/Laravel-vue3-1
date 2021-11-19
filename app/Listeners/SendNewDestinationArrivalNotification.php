<?php

namespace App\Listeners;

use App\Events\NewDestinationArrival;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewDestinationArrivalNotification
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
     * @param  NewDestinationArrival  $event
     * @return void
     */
    public function handle(NewDestinationArrival $event)
    {
        //
    }
}
