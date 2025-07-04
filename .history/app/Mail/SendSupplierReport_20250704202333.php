<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendSupplierReport extends Mailable
{
    use Queueable, SerializesModels;
    public $supplier;
    public $pendingOrdersCount;
    public $deliveredOrdersCount;
    public $confirmedOrdersCount;
    public $cancelledOrdersCount;
    public $totalOrdersCount;
    public $totalSales;
    public $reportDate;
    public function __construct($supplier, $pendingOrdersCount, $deliveredOrdersCount, $confirmedOrdersCount, $cancelledOrdersCount, $totalOrdersCount, $totalSales, $reportDate)
    {
        $this->supplier = $supplier;
        $this->pendingOrdersCount = $pendingOrdersCount;
        $this->deliveredOrdersCount = $deliveredOrdersCount;
        $this->confirmedOrdersCount = $confirmedOrdersCount;
        $this->cancelledOrdersCount = $cancelledOrdersCount;
        $this->totalOrdersCount = $totalOrdersCount;
        $this->totalSales = $totalSales;
        $this->reportDate = $reportDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Suppp',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.supplier_report',
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