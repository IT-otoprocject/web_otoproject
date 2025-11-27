<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProtectProductionDatabase extends Command
{
    protected $signature = 'db:protect-production';
    protected $description = 'Protect production database from dangerous operations';

    public function handle()
    {
        if (config('app.env') === 'production') {
            // Prevent dangerous migrations in production
            $dangerousCommands = [
                'migrate:fresh',
                'migrate:reset', 
                'migrate:refresh',
                'db:wipe'
            ];
            
            foreach ($dangerousCommands as $command) {
                $this->info("Dangerous command '$command' is BLOCKED in production");
            }
            
            $this->info('Production database is protected!');
            return;
        }
        
        $this->info('This is not production environment. Commands are allowed.');
    }
}
