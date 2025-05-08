<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderCompletedForCustomer;
use App\Mail\OrderNotificationForOperator;
use App\Models\Operator;
use Illuminate\Contracts\Queue\ShouldQueue; // Keep for potential future use
use Illuminate\Queue\InteractsWithQueue; // Keep for potential future use
use Illuminate\Support\Facades\Mail;

class SendOrderNotification // Potentially implements ShouldQueue later
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $customer = $order->customer; // Assuming relation exists

        // Send email to customer
        if ($customer && $customer->email) {
            Mail::to($customer->email)->send(new OrderCompletedForCustomer($order));
        }

        // Send email to operators
        $operators = Operator::all(); // Consider filtering later
        foreach ($operators as $operator) {
            if ($operator->email) {
                Mail::to($operator->email)->send(new OrderNotificationForOperator($order));
            }
        }
    }
}
