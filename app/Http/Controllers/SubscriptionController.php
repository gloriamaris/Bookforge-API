<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ValidationController;
use App\User;
use App\ConsumerToken;
use Illuminate\Http\Request; 


class SubscriptionController extends Controller
{ 
    private $userId;
    
    public function __construct(Request $request) {
        $validation = new ValidationController($request);
        $this->userId = $validation->getUserId();
    }
    /**
     * Create new user subscription
     *
     * @param Request $request
     * @return json
     */
    public function createSubscription (Request $request) { 
        $user = User::where('id', $this->userId)->first(); 

        $plan = $request->json('package'); 

        \Stripe\Stripe::setApiKey($user->getStripeKey());

        $stripeToken = \Stripe\Token::create(array(
            "card" => array(
                "number" => $request->json('number'),
                "exp_month" => $request->json('exp_month'),
                "exp_year" => $request->json('exp_year'),
                "cvc" => $request->json('cvc')
            )
        )); 
        $user->newSubscription('main', $plan)->create($stripeToken->id); 

        return response()->json($user);
    } 
}
