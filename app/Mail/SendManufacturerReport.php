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
    public function __construct()
    {
        $this->user = func_get_arg(0);
        $this->deliveredCount = func_get_arg(1);
        $this->pendingCount = func_get_arg(2);
        $this->confirmedCount = func_get_arg(3);
        $this->cancelledCount = func_get_arg(4);
        $this->totalCount = func_get_arg(5);
        $this->still = func_get_arg(6);
        $this->low = func_get_arg(7);
        $this->out = func_get_arg(8);   
        $this->date = func_get_arg(9);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Manufacturer\'s Weekly Report',
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
