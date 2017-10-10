<?php

namespace App\Http\Middleware; 

use App\Consumer; 
use App\ConsumerToken; 
use App\Exceptions\APIHttpException;
use Closure;

class ValidateClientRequest
{

    /**
     * Gets the consumer key from the headers, if present
     * else gets from the server
     * @param  Request $request
     * @return string $key
     */
    public function getConsumerKey($request) {
        $key = $request->header('consumerKey');

        if (!$key) {
            $key = $request->server('consumerKey');
        } 

        return $key;
    } 

    /**
     * Gets the consumer secret from the headers, if present
     * else gets from the server
     * @param  Request $request from header
     * @return string $secret
     */
    public function getConsumerSecret($request) {
        $secret = $request->header('consumerSecret'); 

        if (!$secret) {
            $secret = $request->server('consumerSecret');
        }

        return $secret;
    } 

    // public function getAccessToken($request) {
    //     $token = $request->header('Authorization'); 

    //     if (!$token) {
    //         $token = $request->server('Authorization');
    //     } 

    //     $token = explode(" ", $token);
    //     $token = $token[1];

    //     return $token;
    // }

    /**
     * Validates the consumer key and secret
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $consumerKey = $this->getConsumerKey($request); 
        $consumerSecret = $this->getConsumerSecret($request); 
        // $accessToken = $this->getAccessToken($request);

        if (!$consumerKey && !$consumerSecret) {
            throw new APIHttpException(401, "Missing consumer credentials", null, ['parameters' => 'headers']);
        } 

        $consumer = Consumer::where([ 
            'key' => $consumerKey,
            'secret' => $consumerSecret,
        ])->first(); 

        if (!$consumer) {
            throw new APIHttpException(401, "Invalid consumer credentials", null, ['parameters' => 'headers']);
        } 

        // $consumerToken = ConsumerToken::where([
        //     'consumer_id' => $consumer->id,
        //     'access_token' => $accessToken,
        // ])->first(); 

        // if (!$consumerToken) {
        //     throw new APIHttpException(401, "Error Processing Request", null, ['parameters' => 'Authorization']);
            
        // }

        return $next($request);
    }
}
