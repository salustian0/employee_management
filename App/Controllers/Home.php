<?php
namespace App\Controllers;

use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Session;
use App\Views\View;

class Home{

    static function index(){
        Login::verifyAuth();
        $view = new View();
        $view->render('home');
    }

}