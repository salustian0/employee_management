<?php
namespace  App\Config;
use \App\Controllers as Ctrl;
use App\system\http\Request;
use App\system\http\Router;
if(!(isset($router) && $router instanceof Router) ) die("Houve um erro na tentativa de registro das rotas");


/**
 * Rota padrão
 */
$router->addGetRoute('/', function(){
    return Ctrl\Home::index();
});

/**
 * Rotas usuários
 */
$router->addGetRoute('usuarios', function(Request $request){
    return Ctrl\User::list($request);
});

$router->addGetRoute('usuarios/form', function(){
    return Ctrl\User::form();
});

$router->addPostRoute('usuarios/registrar', function(Request $request){
    return Ctrl\User::register($request);
});

$router->addGetRoute('usuarios/atualizar/{idUser}', function($idUser){
    return Ctrl\User::form($idUser);
});

$router->addGetRoute('usuarios/delete/{idUser}', function($idUser){
    return Ctrl\User::delete($idUser);
});
