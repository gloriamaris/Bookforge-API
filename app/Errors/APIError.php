<?php 

/**
* API Error class
*/
class APIError
{
	public $httpStatus; 
	public $title; 
	public $detail; 
	public $source; 
	
	public function __construct($httpStatus, $title, $detail, $source) 
	{
		$this->httpStatus = $httpStatus;
		$this->title = $title;
		$this->detail = $detail;
		$this->source = $source;
	}
}