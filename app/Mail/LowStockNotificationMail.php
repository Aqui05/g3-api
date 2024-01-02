<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $productName;

    /**
     * Create a new message instance.
     */
    public function __construct($productName)
{
    $this->productName = $productName;
}

public function build()
{
    return $this->subject('Notification de stock faible')
    ->markdown('Email.lowStockNotif')
    ->with(['productName'=>$this->productName]) ;
}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Stock Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'Email.lowStockNotif',
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
