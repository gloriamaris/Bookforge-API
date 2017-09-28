<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    protected $table = 'consumers'; 

    public static function generateKeys() {

    	$generated = [
    		'key' => substr(md5(mt_rand() . time()), -20), 
    		'secret' => substr(md5(mt_rand() . time()), -40)
    		]; 

    	return $generated;
    }
}
