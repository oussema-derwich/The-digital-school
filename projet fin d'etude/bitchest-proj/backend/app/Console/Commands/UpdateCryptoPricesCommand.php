<?php

namespace App\Console\Commands;

use App\Jobs\UpdateCryptoPrices;
use Illuminate\Console\Command;

class UpdateCryptoPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:update-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour les prix des cryptomonnaies depuis une API externe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Démarrage de la mise à jour des prix des cryptomonnaies...');
        
        // Dispatch le job
        UpdateCryptoPrices::dispatch();
        
        $this->info('Job de mise à jour des prix envoyé avec succès.');
    }
}
