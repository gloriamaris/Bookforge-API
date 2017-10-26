<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ValidationController;
use App\User;
use App\ConsumerToken;
use App\Subscription;
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
        
        if (!$user->subscription_id || $user->subscription('main')->cancelled()) { 
            
            $stripeToken = \Stripe\Token::create(array(
                "card" => array(
                    "number" => $request->json('number'),
                    "exp_month" => $request->json('exp_month'),
                    "exp_year" => $request->json('exp_year'),
                    "cvc" => $request->json('cvc')
                )
            )); 
            $subId = $user->newSubscription('main', $plan)->create($stripeToken->id); 
            $user->subscription_id = $subId->id;
            $user->save();

            $subId->status = 'active';
            $subId->save();
        } else {
            return response()->json('Already subscribed!');
        } 

            return response()->json($user);
    } 
    
    /**
     * Change user subscription
     *
     * @param Request $request
     * @return void
     */
    public function upgradeSubscription (Request $request) {
        $user = User::where('id', $this->userId)->first(); 
        $plan = $request->json('package'); 
        $subscription = Subscription::where('user_id', $this->userId)->first(); 
        \Stripe\Stripe::setApiKey($user->getStripeKey()); 

        if (\Stripe\Subscription::retrieve($subscription->stripe_id)) {
            $subscription = $user->subscription('main')->swap($plan);      
        } else {
            return response()->json('No subscription found');
        }
        
        return response()->json($subscription);
    } 
    /**
     * Generate list of user's invoice
     *
     * @return void
     */
    public function getInvoice() {
        $user = User::where('id', $this->userId)->first(); 
        \Stripe\Stripe::setApiKey($user->getStripeKey()); 

        return response()->json(\Stripe\Invoice::all());
        
    } 

    /**
     * Cancels user subscription
     *
     * @return void
     */
    public function cancelSubscription () {
        $user = User::where('id', $this->userId)->first(); 
        $subscription = Subscription::where('id', $user->subscription_id)->first(); 
        \Stripe\Stripe::setApiKey($user->getStripeKey()); 

        if ($user->subscription_id) { 
            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_id);

            if ($stripeSubscription->status === 'active') {
                $stripeSubscription->cancel(); 

                $subscription->status = 'canceled';
                $subscription->save();

                $user->subscription_id = null;
                $user->save(); 
                return response()->json($subscription);
            } 
            else if ($subscription->status === 'canceled'){
                return response()->json('Already cancelled!');
            }
        } else {
            return response()->json('Not subscribed!');
        }
        return response()->json($user);
    }
}
