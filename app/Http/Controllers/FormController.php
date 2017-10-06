<?php 

namespace App\Http\Controllers; 

use App\Consumer; 
use App\ConsumerToken; 
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Exceptions\APIHttpException;


class FormController extends Controller{


	/**
	 * Checks if the form fields has values
	 * @param  Require $request 
	 * @param array $requiredField
	 * @return array [fields, values]
	 */
	public static function validateFormData($requiredField, $request) {

		foreach ($requiredField as $field) {
			$value = $request->json($field);

			if (!isset($value)) {
				$errorMessage = 'Missing form data';
				$errorDetails = $field . ' required';
				throw new APIHttpException(400, $errorMessage, $errorDetails, ['parameter' => $field]);
				
			}
		}
		return $request->all();

	}
}