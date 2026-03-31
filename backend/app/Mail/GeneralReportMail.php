<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function build()
    {
        $periodLabels = [
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'yearly' => 'Anual'
        ];
        
        $periodName = $periodLabels[$this->reportData['period']] ?? 'General';
        
        return $this->subject("Reporte de Ventas {$periodName} - Soft Admin")
                    ->view('emails.general_report');
    }
}
