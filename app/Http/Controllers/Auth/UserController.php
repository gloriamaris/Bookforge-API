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
    /**
     * Signs up the user using valid credentials.
     * @param  Request $request
     * @return Response json
     */
    public function signup(Request $request)
    {
        $requiredFields = ['username', 'firstname', 'lastname', 'email', 'password'];
        $form = FormController::validateFormData($requiredFields, $request);

        try {

            DB::beginTransaction();

                $generated = User::generateToken();

                $user = new User;
                $user->username = $request->input('username');
                $user->first_name = $request->input('firstname');
                $user->last_name = $request->input('lastname');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->remember_token = $generated['remember_token'];
                $user->access_token = $generated['access_token'];
                $user->save();

                $response = $this->login($request);

            DB::commit();

            return $response;
        } catch (\Exception $e) {

            DB::rollback();

            if (strpos($e->getMessage(), 'user_exists')) {
                $response = $this->login($request);
                return $response;
            } else {
              $errorCode = $e->getCode();
              $errorMsg = $e->getMessage();
              if ($e->errorInfo[1] === 1062){
                $errorDetails = 'Email address is already taken. Please try another email.';
              } else
              $errorDetails = 'Something went wrong with the execution while signing up.';
              $errorFields = $requiredFields;

              $error = [
                  'error_code' => $e->errorInfo[1],
                  'status' => $errorCode,
                  'message' => $errorMsg,
                  'details' => $errorDetails,
                  'parameters' => $errorFields
              ];

              return response()->json($error);
            }
        }
    }

    /**
     *
     * Authenticates the user's email, password and consumer key and secret
     * @param Request $request
     * @return Response json
     */

    public function login(Request $request)
    {

        $expire = 86400;

        $requiredFields = ['email', 'password'];
        $form = FormController::validateFormData($requiredFields, $request);

        $user = User::where('email', $form['email'])->first(); 

        if (!isset($user)) { 
            $errorCode = 403;
            $errorMsg = 'Invalid credentials. No user found';
            $errorDetails = 'Wrong email or password.';
            $errorFields = ['email', 'password']; 

            throw new APIHttpException($errorCode, $errorMsg, $errorDetails, ['parameters' => $errorFields]);
        } else if (!Hash::check($form['password'], $user->password)) {
            $errorCode = 403;
            $errorMsg = 'Invalid credentials. Wrong password';
            $errorDetails = 'Wrong password.';
            $errorFields = ['password']; 

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

        $data = [
            'data' => $user,
            'meta' => $meta
        ];

        return response()->json($data);
    }

    /**
     *
     * Stores the access token of the user to the database.
     * @param int $userId
     * @param String $consumerId
     * @param String $accessToken
     * @param String $status
     * @param int $expiresIn
     * @return Response json
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
                $errorFields = ['user_id', 'consumer_id'];

                $error = [
                    'status' => $errorCode,
                    'message' => $errorMsg,
                    'details' => $errorDetails,
                    'parameters' => $errorFields
                ];

                return response()->json($error);
            }
        }
        return $auth;
    }
}
