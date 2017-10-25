<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;

class ValidationController extends Controller
{
    protected $userId; 

    /**
     * Gets the user id of the client accessing a resource
     * using consumer credentials.
     * 
     * @param Request $request
     */

    public function __construct(Request $request) { 
        $consumer = new ConsumerController(
            $request->header('consumerKey'),
            $request->header('consumerSecret'), 
            $request->header('Authorization')); 
        
        $id = $consumer->getUserIdByToken(); 
        $this->setUserId($id); 
        
    } 

    /**
     * Sets the user id 
     * 
     * @param int $userId
     */

    public function setUserId($id) {
        $this->userId = $id;
    } 

    /**
     * Gets the user id
     * 
     * @return int $userId
     */
    public function getUserId() {
        return $this->userId;
    }
}
