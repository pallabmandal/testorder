<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Log;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Customer Import Initiated');

        Excel::import(new UsersImport, storage_path('customers.csv'));
        UsersImport::logCount();
        
        Log::info('Customer Import Completed');
        return Command::SUCCESS;
    }
}
