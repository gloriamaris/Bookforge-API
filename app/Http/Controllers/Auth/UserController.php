<?php 

namespace App\Http\Controllers\Auth;

use App\User;
use App\Consumer;
use App\ConsumerToken;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;

class UserController extends Controller 
{

    public function signup(Request $request)
    {
        /**
         *
         * validates the user's inputs 
         * 
         */
        $this->validate($request, [
            'uname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        

        /**
         * 
         * Create a new user instance after a valid registration.
         *
         */
        $generated = User::generateToken(); 
        
        $user = new User;
        $user->username = $request->input('uname');
        $user->first_name = $request->input('fname');
        $user->last_name = $request->input('lname');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->remember_token = $generated['remember_token'];
        $user->access_token = $generated['access_token'];
        $user->save();

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
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]); 

        $user = User::where([
            'email' =>$request->input('email'),
            'password' =>$request->input('password'),
        ])->first(); 

        $consumer = Consumer::where([
            'key' => $request->header('consumerKey'),
            'secret' => $request->header('consumerSecret'),
        ])->first(); 

        $auth = $this->storeToken($user->id, $consumer->id, $response['access_token'], 'active', $response['expires_in']);

        return response()->json([
                'message' => 'Welcome',
        ], 201);
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
            $auth = new ConsumerToken; 

            $auth->user_id = $userId;
            $auth->consumer_id = $consumerId;
            $auth->token = $accessToken;
            $auth->status = $status;
            $auth->expires_in = $expiresIn;

            $auth->save();
        }
    }
}