<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Hash;
use App\User;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'General 1st time setup stuff';

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
        // Create a generic admin account with default password
        $user = \App\User::where('email', 'john.glen.stevens@gmail.com')->first();
        if (!is_object($user) || $user->email == '') {
            $user = new User();
            $user->password = Hash::make('pleasechangemelater');
            $user->email = env('ADMIN_EMAIL', 'john.glen.stevens@gmail.com');
            $user->name = env('ADMIN_NAME', 'john.glen.stevens@gmail.com');
            $user->save();
        } else {
            echo "\nALREADY CREATED THE ADMIN\n";
            echo $user->email . "\n";
        }
    }
}
