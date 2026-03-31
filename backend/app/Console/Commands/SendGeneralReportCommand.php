<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportService;
use App\Mail\GeneralReportMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendGeneralReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send {period=weekly : The period of the report (weekly, monthly, yearly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the general sales and financial report via email';

    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService)
    {
        $period = $this->argument('period');
        
        if (!in_array($period, ['weekly', 'monthly', 'yearly'])) {
            $this->error('Invalid period. Allowed values: weekly, monthly, yearly.');
            return 1;
        }

        $this->info("Generating {$period} report...");

        try {
            $reportData = $reportService->getGeneralReportData($period);
            
            $toEmail = 'emiliovpsis@gmail.com';
            
            Mail::to($toEmail)->send(new GeneralReportMail($reportData));
            
            $this->info("Report sent successfully to {$toEmail}!");
            return 0;
            
        } catch (Exception $e) {
            $this->error("Failed to send report: " . $e->getMessage());
            return 1;
        }
    }
}
