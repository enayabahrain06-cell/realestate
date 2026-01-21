<?php

namespace App\Console\Commands;

use App\Services\RealEstate\FinancialService;
use Illuminate\Console\Command;

class ApplyLateFees extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fees:apply-late {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Apply late fees to overdue lease payments';

    /**
     * Financial service instance.
     */
    protected FinancialService $financialService;

    /**
     * Create a new command instance.
     */
    public function __construct(FinancialService $financialService)
    {
        parent::__construct();
        $this->financialService = $financialService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info("DRY RUN - No changes will be made");
            $this->info("Checking for overdue leases...");
        } else {
            $this->info("Applying late fees to overdue leases...");
        }

        try {
            $overdueLeases = \App\Models\Lease::where('status', 'active')
                ->whereDate('start_date', '<', now()->subDays(5)->subMonth())
                ->with('tenant')
                ->get();

            $count = 0;
            $totalFees = 0;

            foreach ($overdueLeases as $lease) {
                $lateFeeAmount = $this->financialService->calculateLateFee($lease->id);
                
                if ($lateFeeAmount > 0) {
                    $existingLateFee = \App\Models\LateFee::where('lease_id', $lease->id)
                        ->where('payment_id', null)
                        ->whereDate('created_at', '>=', now()->subDays(5)->subMonth())
                        ->first();

                    if (!$existingLateFee) {
                        if (!$dryRun) {
                            \App\Models\LateFee::create([
                                'lease_id' => $lease->id,
                                'amount' => $lateFeeAmount,
                                'due_date' => now(),
                                'status' => 'pending'
                            ]);
                        }
                        $count++;
                        $totalFees += $lateFeeAmount;
                        
                        $this->line("Lease #{$lease->id}: {$lease->tenant->full_name} - AED {$lateFeeAmount}");
                    }
                }
            }

            if ($count === 0) {
                $this->info("No overdue leases found requiring late fees.");
            } else {
                if ($dryRun) {
                    $this->info("DRY RUN RESULT: Would apply {$count} late fees totaling AED " . number_format($totalFees, 2));
                } else {
                    $this->info("Successfully applied {$count} late fees totaling AED " . number_format($totalFees, 2));
                }
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error applying late fees: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

