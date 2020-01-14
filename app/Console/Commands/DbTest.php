<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DbTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check DB Settings';

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
     * @return mixed
     */
    public function handle()
    {
        if (DB::connection()) {
            echo "CONNECTION OK  ";
            var_dump( DB::connection()->getPdo() );
        } else {
            echo "NO CONNECTION";
        }
    }
}
