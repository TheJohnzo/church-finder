<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ImportCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import countries from CSV.';

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
        $filename = 'https://raw.githubusercontent.com/datasets/country-codes/master/data/country-codes.csv';

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, ',')) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        DB::table('country')->truncate();
        foreach ($data as $d)
        {
            if ($d['official_name_en'] != '' && $d['ISO3166-1-Alpha-2'] != '') {
                $c = new \App\Country;
                $c->name = $d['official_name_en'];
                $c->code = $d['ISO3166-1-Alpha-2'];
                $c->save();
                $this->info('saving: ' . $d['ISO3166-1-Alpha-2']);
            }
        }
    }
}
