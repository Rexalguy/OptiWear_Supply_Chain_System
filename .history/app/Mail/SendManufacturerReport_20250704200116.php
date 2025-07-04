<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendManufacturerReport extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $deliveredCount;
    public $pendingCount;
    public $confirmedCount;
    public $cancelledCount;
    public $totalCount;
    public $still;
    public $low;
    public $out;    
    public $date;
    public function __construct($user, $deliveredCount, $pendingCount, $confirmedCount, $cancelledCount, $totalCount, $still, $low, $out, $datezzzzzz)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Manufacturer Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.manufacturer_report',
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