<?php 

namespace App\Http\Controllers; 

use App\Consumer; 
use App\ConsumerToken; 
use App\Http\Controllers\Controller;

class ConsumerController extends Controller
{
	private $consumerKey;
	private $consumerSecret;
	private $accessToken;
	
	public function __construct($key = null, $secret = null, $token = null)
	{
		$this->setConsumerKey($key);	
		$this->setConsumerSecret($secret);	
		$this->setAccessToken($token);	
	}

	/**
	 * function to set the consumer key 
	 * @param string $key 
	 */
	public function setConsumerKey($key) {
		$this->consumerKey = $key;
	} 

	/**
	 * function to set theconsumer secret 
	 * @param string $secret
	 */
	public function setConsumerSecret($secret) {
		$this->consumerSecret = $secret;
	} 

	/**
	 * function to set the access token 
	 * @param string $token 
	 */
	public function setAccessToken($token) {
		$accessToken = null;

		if (isset($token)) {
			$accessToken = explode(" ", trim($token))[1];
		} 
		$this->accessToken = $accessToken;
	}

	/**
	 * function to get the consumer key
	 * @return string $consumerKey
	 */
	public function getConsumerKey() {
		$this->consumerKey;
	} 

	/**
	 * function to get the consumer Secret
	 * @return string consumerSecret
	 */
	public function getConsumerSecret() {
		$this->consumerSecret;
	} 

	/**
	 * function to get the access token
	 * @return $string accessToken
	 */
	public function getAccessToken() {
		$this->accessToken;
	}

	/**
	 * function to get the consumer id using key and secret
	 * @return int id
	 */
	public function getConsumerId() {
		try {
			$consumer = Consumer::where([ 
				'key' => $this->getConsumerKey(), 
				'secret' => $this->getConsumerSecret(), 
			])->first(); 

			return $consumer->id;
		} catch (\Exception $e) {
			$error = [
				'status' => 500,
				'message' => 'Invalid consumer key and/or secret.',
				'details' => $e->getMessage(),
				'parameters' => [
						'consumerKey',
						'consumerSecret'
					]
			];

			return response()->json($error);
			
		}
	}

	/**
	 * function to get the user's id using access token
	 * @return [type] [description]
	 */
	public function getUserIdByToken() {
		try {
			$consumerToken = ConsumerToken::where([ 
				'consumer_id' => $this->getConsumerId(), 
				'token' => $this->getAccessToken(), 
			])->first(); 

			return $consumerToken->user_id;
		} catch (\Exception $e) {
			$errorMsg = 'Invalid token'; 
			throw new APIHttpException(400, $errorMsg, $e->getMessage(), ['parameter' => 'token']);
			
			return response()->json($errorMsg);
		}
	}


}