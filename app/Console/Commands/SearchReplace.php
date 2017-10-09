<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SearchReplace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wask:db-search-replace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search and replace command in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $old = $this->ask('Enter string to be replaced');
        $new = $this->ask('Enter new string');
        $sure = $this->ask('Are you sure you want to perform this action? Type "yes" if sure.');

        if($sure!='yes')
        {
            $this->info('Action aborted.');
        }
        else
        {
            $excludeTables = ['migrations','password_resets'];

            $databaseName = env('DB_DATABASE');
            $tables = DB::select('SHOW TABLES');
            $prop = "Tables_in_{$databaseName}";

            $bar = $this->output->createProgressBar(count($tables));

            foreach ($tables as $table)
            {
                $tbl = $table->{$prop};

                if(in_array($tbl,$excludeTables)) continue;

                $columns = Schema::getColumnListing($tbl);

                foreach ($columns as $k => $column)
                {
                    DB::select("UPDATE `{$tbl}` SET `{$column}` = REPLACE(`{$column}`, '{$old}','{$new}')");
                }

                $bar->advance();
            }

            $bar->finish();

            $this->info('Database successfully updated.');
        }
    }
}
