<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SummaryReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $period;
    public $startDate;
    public $endDate;
    public $pdfBytes;

    /**
     * Create a new message instance.
     */
    public function __construct($period, $startDate, $endDate, $pdfBytes)
    {
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->pdfBytes = $pdfBytes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte Financiero ' . ucfirst($this->period) . ' - Soft Admin',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.summary_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, 'Resumen_Corporativo_' . ucfirst($this->period) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
