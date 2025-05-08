<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\Order; // Add this line
use Illuminate\Mail\Mailables\Address; // Add this line
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderNotificationForOperator extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // TODO: Get site name dynamically
        $siteName = $this->order->site->name ?? config('app.name');
        $operatorEmail = config('mail.operator_notification_address'); // Use the new config key

        // The listener now handles the check for a missing operatorEmail,
        // so this Mailable assumes $operatorEmail will be valid if it reaches here.
        // However, as a safeguard or for direct Mailable usage, keeping a check can be useful.
        // If the listener didn't catch the missing config, throw an exception here.
        if (!$operatorEmail) {
            // This case should ideally not be reached if the listener checks first.
            // Throwing an exception ensures misconfiguration doesn't lead to silent failure.
            throw new \RuntimeException('OrderNotificationForOperator Mailable requires a valid operator_notification_address to be configured.');
        }

        // If $operatorEmail could still be null/empty here (e.g., empty string from config),
        // the Address constructor might fail or lead to issues.
        // The listener's isValidOperatorEmail should prevent this, but adding robustness here is possible.
        // Given the listener change, we expect $operatorEmail to be valid.
        // If not, Mail::to() in the listener would have already skipped sending.
        // So, the `to` field here is more of a confirmation if the Mailable itself
        // were to define its recipient independently, which we are moving away from
        // for the primary recipient.
        // For clarity and to rely on the listener's Mail::to(), we can remove the `to` from here
        // or ensure it matches. Let's keep it for now, assuming it will match.

        return new Envelope(
            // The `to` here will be overridden by Mail::to() in the listener.
            // If Mail::to() was not used, this `to` would be the recipient.
            // Since we *are* using Mail::to() in the listener with the correct address,
            // this `to` field in the Mailable's envelope is less critical for the primary recipient.
            // However, it's good practice for it to reflect the intended recipient if known.
            // Since the recipient is specified in the listener using Mail::to(),
            // specifying it here is redundant and potentially confusing. Removing it.
            subject: sprintf('【%s】新規注文のお知らせ (注文コード: %s)', $siteName, $this->order->order_code),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.operator.order_notification',
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
