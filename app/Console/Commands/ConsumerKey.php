<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ConsumerKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer:generateKeys {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate consumer\'s key';

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
        $conName = $this->argument('name'); 

        $this->info('Generating consumer key and secret for ' . $conName); 

        $conSecret = substr(md5(mt_rand() . time()), -20);
        $conKey = substr(md5(mt_rand() . time()), -40);


        DB::table('consumers')->insert(
            ['name' => $conName, 'secret' => $conSecret, 'key' => $conKey, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]
        ); 

        $this->info('Consumer key and secret has been generated'); 
        $this->info('Consumer Name: ' . $conName);
        $this->info('Key: ' . $conKey);
        $this->info('Secret: ' . $conSecret);
        $this->info('Done.');
    }
}
