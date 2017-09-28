<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
	/**
	 * Set table name for Consumer class 
	 * @var string
	 */
    protected $table = 'consumers'; 

    /**
     * Generate key and secret
     * @return array
     */
    public static function generateKeys() {

    	$generated = [
    		'key' => substr(md5(mt_rand() . time()), -20), 
    		'secret' => substr(md5(mt_rand() . time()), -40)
    		]; 

    	return $generated;
    }
}
