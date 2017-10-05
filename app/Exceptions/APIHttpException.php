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

	/**
	 * function to get exception status code
	 * @return int $statusCode
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	} 

	/**
	 * function to get error message
	 * @return string $msg
	 */
	public function getErrorMessage()
	{
		return $this->msg;
	} 

	/**
	 * function to get the error details
	 * @return string $details
	 */
	public function getErrorDetails()
	{
		return $this->details;
	} 

	/**
	 * function to get the error source
	 * @return array $source
	 */
	public function getSource()
	{
		return $this->source;
	} 

	/**
	 * function to get the API error
	 * @return object apiError
	 */
	public function getAPIError()
	{
		return $this->apiError;
	}
}