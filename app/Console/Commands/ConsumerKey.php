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
    protected $signature = 'consumer:generateKeys';

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
        $conSecret = $this->ask('Enter secret: ');
        $conKey = $this->ask('Enter key: '); 

        DB::table('consumers')->insert(
            ['name' => 'jeana', 'secret' => $conSecret, 'key' => $conKey]
        ); 

        $this->info('Secret and key has been generated');
    }
}
