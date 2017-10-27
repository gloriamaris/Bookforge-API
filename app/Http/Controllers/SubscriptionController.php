<?php

namespace App\Http\Controllers;

use App\Exceptions\APIHttpException;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\FormController;
use App\User;
use App\ConsumerToken;
use App\Subscription;
use DB;
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
        $requiredFields = ['cardNumber', 'expiryMonth', 'expiryYear', 'cvc', 'package']; 
        $form = FormController::validateFormData($requiredFields, $request); 

        $cardData = [
            'card' => [
                'number' => $form['cardNumber'], 
                'exp_month' => $form['expiryMonth'],
                'exp_year' => $form['expiryYear'], 
                'cvc' => $form['cvc']
            ]
        ];

        $user = User::where('id', $this->userId)->first(); 

        try {
            \Stripe\Stripe::setApiKey($user->getStripeKey());
            if (!$user->subscription_id || $user->subscription('main')->cancelled()) { 
                try {
                    DB::beginTransaction();
                    $stripeToken = \Stripe\Token::create($cardData); 
                    $subId = $user->newSubscription('main', $form['package'])->create($stripeToken->id); 
                    $user->subscription_id = $subId->id;
                    $user->save();

                    DB::commit(); 

                    try {
                        DB::beginTransaction();
        
                        $subId->status = 'active';
                        $subId->save();
        
                        DB::commit();
                    } catch (\Exception $e){
                        DB::rollback();
        
                        $errorMsg = 'There was an error in setting the subscription status.'; 
                        $errorDetails = $e->getMessage(); 
        
                        throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subId]);
        
                    }

                } catch (\Stripe\Error\Base $e) {
                    DB::rollback();
                    return response()->json($e->getMessage());
                }
                  
            } else { 
                $errorMsg = 'Cannot create user subscription.';
                $errorDetails = 'You are already subscribed!';
                throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $user]);
            }
            
            
        } catch (\Stripe\Error\Base $e) {
            DB::rollback();

            throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $requiredFields]);
        } 

        $user = [
            'username' => $user->username,
            'stripe_plan' => $subId->stripe_plan,
            'status' => $subId->status
        ];
        return response()->json($user);
    } 
    
    /**
     * Change user subscription
     *
     * @param Request $request
     * @return void
     */
    public function upgradeSubscription (Request $request) { 
        $requiredFields = ['package']; 
        $form = FormController::validateFormData($requiredFields, $request);

        $user = User::where('id', $this->userId)->first(); 
        $subscription = Subscription::where('user_id', $this->userId)->first();
        try {
            DB::beginTransaction();
            \Stripe\Stripe::setApiKey($user->getStripeKey()); 
            
            if (\Stripe\Subscription::retrieve($subscription->stripe_id)) {
                $subscription = $user->subscription('main')->swap($form['package']);  
                DB::commit();    
            } else { 
                DB::rollback();
                $errorMsg = 'Cannot update user subscription.';
                $errorDetails = 'There was an error updating your subscription.';
                throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $user]);
            }
        } catch (\Stripe\Error\Base $e) { 
            
            return response()->json($e->getMessage());
        } 

        $user = [
            'username' => $user->username,
            'stripe_plan' => $subscription->stripe_plan,
            'status' => $subscription->status
        ];
        return response()->json($user);

    } 
    /**
     * Generate list of user's invoice
     *
     * @return void
     */
    public function getInvoice() {
        $user = User::where('id', $this->userId)->first(); 
        try {
        \Stripe\Stripe::setApiKey($user->getStripeKey()); 

        } catch (\Stripe\Error\Base $e) { 
            return response()->json($e->getMessage());
        } 

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
        
        try {
            \Stripe\Stripe::setApiKey($user->getStripeKey()); 

            if ($user->subscription_id !== null) {

                $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_id);
                if ($stripeSubscription->status === 'active') { 
                
                    try { 
                        DB::beginTransaction();
                        $stripeSubscription->cancel();                         
                        $subscription->status = 'canceled';
                        $subscription->save(); 
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        $errorMsg = 'Failed to cancel subscription status in the database';
                        $errorDetails = $e->getMessage();
                        throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subscription]);
                    }
                    try { 
                        DB::beginTransaction();
                        $user->subscription_id = null;
                        $user->save(); 
                        DB::commit();
                
                    } catch (\Exception $e) {
                        DB::rollback();
                        $errorMsg = 'Failed to cancel subscription status in the database';
                        $errorDetails = $e->getMessage();
                        throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subscription]);
                    }


                    $user = [
                        'username' => $user->username,
                        'stripe_plan' => $subscription->stripe_plan,
                        'status' => $subscription->status
                    ];
                    return response()->json($user);

                } 
                
                else if ($subscription->status === 'canceled'){
                    $errorMsg = 'Subscription is already cancelled.';
                    $errorDetails = 'Subscription status is already cancelled.';
                    throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subscription]);

                } else if (!$stripeSubscription){
                    $errorMsg = 'No subscription found';
                    $errorDetails = 'Subscription id does not exist!';
                    throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subscription]);
                }
            } else {
                $errorMsg = 'Cannot cancel user subscription.';
                $errorDetails = 'There was an error in cancelling your subscription.';
                throw new APIHttpException(400, $errorMsg, $errorDetails, ['parameters' => $subscription]);
            }
        } catch (\Stripe\Error\Base $e) {

            return response()->json($e->getMessage());
        }
    }

}
