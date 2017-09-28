<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Consumer;

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
    protected $description = 'Generate consumer key and secret';

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

        $generated = Consumer::generateKeys(); 
        $consumer = new Consumer; 
        $consumer->name = $conName; 

        if (Consumer::where('name', $conName)->exists()){
            $this->error('Consumer name already exist. Try another consumer key.');
            return;

        } else {
            $this->info('Generating consumer key and secret for ' . $conName); 
            $consumer->key = $generated['key'];
            $consumer->secret = $generated['secret']; 
            $consumer->save();
            $this->info('Consumer Name: ' . $conName);
            $this->info('Key: ' . $generated['key']);
            $this->info('Secret: ' . $generated['secret']);
            
        }  

        
    }
}
