<?php

namespace App\Console\Commands;

use App\Http\Services\AlertService;
use Illuminate\Console\Command;

class CheckPriceAlerts extends Command
{
    protected $signature = 'check:price-alerts';
    protected $description = 'Check if crypto prices have reached alert levels';

    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        parent::__construct();
        $this->alertService = $alertService;
    }

    public function handle()
    {
        $this->alertService->checkPriceAlerts();
        $this->info('Price alerts checked successfully.');
    }
}
