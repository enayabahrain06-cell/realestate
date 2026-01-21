<?php

namespace App\Console\Commands;

use App\Services\RealEstate\DocumentService;
use Illuminate\Console\Command;

class SendExpiryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alerts:send-expiry {--days=30 : Number of days to check for expiring documents}';

    /**
     * The console command description.
     */
    protected $description = 'Send expiry alerts for documents expiring within the specified days';

    /**
     * Document service instance.
     */
    protected DocumentService $documentService;

    /**
     * Create a new command instance.
     */
    public function __construct(DocumentService $documentService)
    {
        parent::__construct();
        $this->documentService = $documentService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        
        $this->info("Checking for documents expiring within {$days} days...");

        try {
            $count = $this->documentService->sendExpiryAlerts();
            
            $this->info("Successfully sent {$count} expiry alerts.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error sending expiry alerts: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

