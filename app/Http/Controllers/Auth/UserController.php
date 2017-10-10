<?php 

namespace App\Http\Controllers\Auth;

use App\User;
use App\Consumer;
use App\ConsumerToken;
use App\Http\Controllers\Controller; 
use App\Http\Controllers\FormController; 
use App\Http\Controllers\Auth\AccessTokenController; 
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Exceptions\APIHttpException;

class UserController extends Controller 
{ 

    public function signup(Request $request)
    {
        /**
         *
         * validates the user's inputs 
         *  
         */
        $requiredFields = ['uname', 'fname', 'lname', 'email', 'password'];
        $form = FormController::validateFormData($requiredFields, $request);
        
        /**
         * 
         * Create a new user instance after a valid registration.
         *
         */
        try {
            
            DB::beginTransaction();

                $generated = User::generateToken(); 
            
                $user = new User;
                $user->username = $request->input('uname');
                $user->first_name = $request->input('fname');
                $user->last_name = $request->input('lname');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->remember_token = $generated['remember_token'];
                $user->access_token = $generated['access_token'];
                $user->save(); 

                $response = $this->login($request); 
                // var_dump($response);die();

            DB::commit();

            return $response;

        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'user_exists')) {
                $data = $this->login($request); 
                return $data;
            } else {

            DB::rollback();
            $errorCode = 400; 
            $errorMsg = $e->getMessage(); 
            $errorDetails = "Something went wrong with the execution while signing up."; 
            $errorFields = $request; 

            $error = [
                'status' => $errorCode, 
                'message' => $errorMsg, 
                'details' => $errorDetails, 
                'parameters' => $errorFields
            ];

            return response()->json($error);

            }
        }

        return response()->json([
                'message' => 'Successfully created a user!'
        ], 201);
    } 

    /**
     *
     * Authenticates the user's email, password and consumer's key and secret
     * 
     */

    public function login(Request $request)
    {   
        
        $expire = 86400;

        $requiredFields = ['email', 'password'];
        $form = FormController::validateFormData($requiredFields, $request);

        $user = User::where([
            'email' => $form['email'],
            'password' => Hash::check('plain-text' ,$form['password']),
        ])->first(); 

        if (!isset($user)) {
            $errorCode = 403;
            $errorMsg = 'Invalid credentials.';
            $errorDetails = 'Wrong email or password.';
            $errorFields = ['email', 'password'];

            throw new APIHttpException($errorCode, $errorMsg, $errorDetails, ['parameters' => $errorFields]);
        }

        $consumer = Consumer::where([
            'key' => $request->header('consumerKey'),
            'secret' => $request->header('consumerSecret'),
        ])->first(); 

        if (!isset($consumer)) {
            $errorCode = 403;
            $errorMsg = 'Invalid credentials.';
            $errorDetails = 'Wrong consumer key and/or secret';
            $errorFields = ['key', 'secret'];

            throw new APIHttpException($errorCode, $errorMsg, $errorDetails, ['parameters' => $errorFields]);
        }

        $meta = $this->storeToken($user->id, $consumer->id, $user['access_token'], 'active', $expire); 
        
        // var_dump($meta);die();

        $data = [
            'data' => $user,
            'meta' => $meta
        ];

        return response()->json($data);
    } 

    /**
     *
     * Stores the access_token of the user
     * 
     */

    public function storeToken($userId, $consumerId, $accessToken, $status, $expiresIn) 
    {
        $auth = ConsumerToken::where([ 
            'user_id' => $userId, 
            'consumer_id' => $consumerId, 
            'token' => $accessToken, 
        ])->first(); 

        if (!isset($auth)) {

            try {
                DB::beginTransaction();
                $auth = new ConsumerToken; 

                $auth->user_id = $userId;
                $auth->consumer_id = $consumerId;
                $auth->token = $accessToken;
                $auth->status = $status;
                $auth->expires_in = $expiresIn;

                $auth->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();

                $errorCode = 400; 
                $errorMsg = $e->getMessage(); 
                $errorDetails = "Something went wrong with the execution."; 
                $errorFields = $request; 

                $error = [
                    'status' => $errorCode, 
                    'message' => $errorMsg, 
                    'details' => $errorDetails, 
                    'parameters' => $errorFields
                ];

            return response()->json($error);


            }
        }
    }
}