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
        \Log::info('[SendOrderNotification] Handle method called.');
        $order = $event->order;
        $customer = $order->customer; // Assuming relation exists

        // Send email to customer
        if ($customer && $customer->email) {
            \Log::info('[SendOrderNotification] Attempting to send email to customer: ' . $customer->email);
            Mail::to($customer->email)->send(new OrderCompletedForCustomer($order));
            \Log::info('[SendOrderNotification] Email to customer sent (or queued).');
        } else {
            \Log::info('[SendOrderNotification] Customer email not found or customer does not exist. Skipping customer email.');
        }

        // Send email to operators
        $operatorEmail = config('mail.operator_notification_address');
        \Log::info('[SendOrderNotification] Operator notification email from config: \'' . $operatorEmail . '\'');

        if ($this->isValidOperatorEmail($operatorEmail)) {
            \Log::info('[SendOrderNotification] Attempting to send email to operator: ' . $operatorEmail);
            Mail::to($operatorEmail)->send(new OrderNotificationForOperator($order));
            \Log::info('[SendOrderNotification] Email to operator sent (or queued).');
        } else {
            \Log::error('[SendOrderNotification] Operator notification email is not configured, invalid, or is the default fallback. Email not sent. Value: \'' . $operatorEmail . '\'');
        }
    }

    /**
     * Validate the operator email address.
     *
     * @param string|null $email
     * @return boolean
     */
    private function isValidOperatorEmail(?string $email): bool
    {
        // Check if the email is not empty, is a valid email format,
        // and is not the default fallback email address.
        $fallbackEmail = config('mail.from.address', 'your-default-fallback-email@example.com'); // Default fallback from general mail config
        // More specific fallback for operator_notification_address if defined differently,
        // but we used 'your-default-fallback-email@example.com' in its definition.
        $operatorConfigFallback = 'your-default-fallback-email@example.com';

        return !empty($email) &&
               filter_var($email, FILTER_VALIDATE_EMAIL) &&
               $email !== $operatorConfigFallback;
    }
}
