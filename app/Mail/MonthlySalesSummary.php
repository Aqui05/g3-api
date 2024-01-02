<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlySalesSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $totalSales;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $totalSales)
    {
        $this->user = $user;
        $this->totalSales = $totalSales;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Monthly Sales Summary',
        );
    }

    public function build()
    {
        return $this->markdown('emails.monthly_sales_summary')
            ->subject('Résumé mensuel des ventes');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.monthly_sales_summary',
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
