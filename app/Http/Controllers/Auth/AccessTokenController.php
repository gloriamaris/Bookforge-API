<?php 

namespace App\Http\Controllers\Auth;

use App\User;
use App\Consumer;
use App\ConsumerToken;
use App\Http\Controllers\Controller; 
use DB;
use Illuminate\Http\Request;
use App\Exceptions\APIHttpException;

class AccessTokenController extends Controller { 

	/**
	 * Gets access token from headers
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public static function getToken(Request $request)
	{
		$token = $request->header('Authorization');
		if (!$token) {
            $token = $request->server('Authorization');
        } 

        $token = explode(" ", $token);
        $token = $token[1];

        return $token;

	}

}