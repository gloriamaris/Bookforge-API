<?php 

namespace App\Exceptions; 

use App\Errors\APIError; 
use Symfony\Component\HttpKernel\Exception\HttpException; 


class APIHttpException extends HttpException
{
	
	public function __construct( 
						 $statusCode, 
						 $message = null, 
						 $details = null, 
						 array $source = [], 
						 APIError $apiError = null, 
						 \Exception $previous = null, 
						 array $headers = [], 
						 $code = 0
						)
	{
		$this->statusCode = $statusCode; 
		$this->msg = $message; 
		$this->details = $details; 
		$this->source = $source; 
		$this->apiError = $apiError; 

		parent::__construct($statusCode, $message, $previous, $headers, $code);
	} 

	public function getStatusCode()
	{
		return $this->statusCode;
	} 

	public function getErrorMessage()
	{
		return $this->msg;
	} 

	public function getErrorDetails()
	{
		return $this->source;
	} 

	public function getAPIError()
	{
		return $this->apiError;
	}
}