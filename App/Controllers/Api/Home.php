<?php
namespace App\Controllers\Api;

use App\system\http\Request;
use App\system\http\Response;

class Home{


    static function index(Request $request){
        $response = new Response();
        return $response->setContentType('application/json')->setContent($request)->sendResponse();
    }
}