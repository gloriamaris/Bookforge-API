<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConsumerController;
use App\User;
use App\ConsumerToken;
use Illuminate\Http\Request; 


class SubscriptionController extends Controller
{
    public function createSubscription (Request $request) { 
        $authorization = $request->header('Authorization'); 
        $authorization = explode(' ', $authorization)[1]; 
        $user = User::where('access_token', $authorization)->first(); 

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
