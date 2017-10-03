<?php 

namespace App\Http\Controllers; 

use App\Consumer; 
use App\ConsumerToken; 

/**
* 
*/
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

	public function setConsumerKey($key) {
		$this->consumerKey = $key;
	} 

	public function setConsumerSecret($secret) {
		$this->consumerSecret = $secret;
	} 

	public function setAccessToken($token) {
		$accessToken = null;

		if (isset($token)) {
			$accessToken = explode(" ", trim($token))[1];
		} 
		$this->accessToken = $accessToken;
	}

	public function getConsumerKey() {
		$this->consumerKey;
	} 

	public function getConsumerSecret() {
		$this->consumerSecret;
	} 

	public function getAccessToken() {
		$this->accessToken;
	}

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


	public function getUserIdByToken() {
		try {
			$consumerToken = ConsumerToken::where([ 
				'consumer_id' => $this->getConsumerId(), 
				'token' => $this->getAccessToken(), 
			])->first(); 

			return $consumerToken->user_id;
		} catch (\Exception $e) {
			$errorMsg = 'Invalid token'; 
			throw new APIHttpException(400, $errorMsg, $e->getMessage(), ['parameter'] => 'token');
			
		}
	}


}