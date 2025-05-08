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
        $operatorEmail = config('mail.operator_notification_email');

        if (!$operatorEmail) {
            // Log an error or handle the missing configuration appropriately
            \Log::error('Operator notification email address is not configured.');
            // Optionally, prevent sending the email if the address is missing
            // throw new \Exception('Operator notification email address is not configured.');
            $operatorEmail = 'default-operator@example.com'; // Fallback or error handling
        }


        return new Envelope(
            to: [new Address($operatorEmail)],
            subject: sprintf('【%s】新規注文のお知らせ (注文ID: %s)', $siteName, $this->order->id),
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
