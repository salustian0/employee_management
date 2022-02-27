<?php
namespace App\Controllers;
use App\Models\HomeModel;
use App\system\http\Request;
use App\system\http\Response;
use App\Views\View;

class Home extends BaseController {

    /**
     * Página inicial
     */
    static function index(){
        $model = new HomeModel();

        $view = new View();
        $view->render("home");
    }
}