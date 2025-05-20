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
    private const DEFAULT_FALLBACK_EMAIL = 'your-default-fallback-email@example.com';

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

        // orderDetails リレーションと、その中の item リレーションをロード
        $order->load('orderDetails.item');

        $customer = $order->customer; // Assuming relation exists

        // Send email to customer
        if ($customer && $customer->email) {
            Mail::to($customer->email)->send(new OrderCompletedForCustomer($order));
        }

        // Send email to operators
        $operatorEmail = config('mail.operator_notification_address');

        if ($this->isValidOperatorEmail($operatorEmail)) {
            Mail::to($operatorEmail)->send(new OrderNotificationForOperator($order));
        } else {
            // Keep error log as error level
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
        // $fallbackEmail variable was unused.
        $operatorConfigFallback = self::DEFAULT_FALLBACK_EMAIL;

        return !empty($email) &&
               filter_var($email, FILTER_VALIDATE_EMAIL) &&
               $email !== $operatorConfigFallback;
    }
}
