<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\CompanySummaryReportService;
use App\Mail\SummaryReportMail;
use Carbon\Carbon;

class SendPeriodicReports extends Command
{
    /**
     * The name and signature of the console command.
     * periodicidad: weekly, monthly, yearly
     *
     * @var string
     */
    protected $signature = 'reports:send {period=monthly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send the periodic financial summary PDF securely via Email';

    /**
     * Execute the console command.
     */
    public function handle(CompanySummaryReportService $reportService)
    {
        $period = $this->argument('period');
        $this->info("Starting the generation of the {$period} macro-report...");

        // Definir límites exactos basados en la periodicidad extraída
        $startDate = Carbon::now();
        $endDate = Carbon::now();

        switch ($period) {
            case 'weekly':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate = Carbon::now()->subYear()->endOfYear();
                break;
            default:
                $this->error("Periodo no reconocido. Usando monthly fallback.");
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
        }

        // Obtener la dirección destino base del .env
        $adminEmail = config('mail.from.address', 'duokode713@gmail.com');

        $this->info("Fetching data and generating PDF from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}...");
        
        $pdfBytes = $reportService->generateOverviewPdfBytes($startDate, $endDate);

        if (str_starts_with($pdfBytes, "PDF Generation Failed:")) {
            $this->error($pdfBytes);
            return 1;
        }

        $this->info("PDF generated successfully. Attaching to Mail and dispatching to {$adminEmail}...");

        Mail::to($adminEmail)->send(new SummaryReportMail($period, $startDate, $endDate, $pdfBytes));

        $this->info("Mail sent efficiently! Operation concluded.");
        return 0;
    }
}
