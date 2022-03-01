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


$router->addGetRoute('home', function(){
    return  Ctrl\Home::index();
});

/* Rotas de login */
$router->addGetRoute('login', function(){
    return  Ctrl\Login::login();
});

$router->addPostRoute('autenticar', function(Request $request){
    return  Ctrl\Login::auth($request);
});


/*Logout*/
$router->addGetRoute('logout', function(){
    return  Ctrl\Login::logout();
});

/* rotas de funcionários */

$router->addGetRoute('funcionarios', function(){
    return Ctrl\Employee::list();
});

$router->addGetRoute('funcionarios/form', function(){
    return Ctrl\Employee::form();
});

$router->addPostRoute('funcionarios/registrar', function(Request $request){
    return Ctrl\Employee::register($request);
});

$router->addGetRoute('funcionarios/atualizar/{idEmployee}', function($idEmployee){
    return Ctrl\Employee::form($idEmployee);
});

$router->addGetRoute('funcionarios/delete/{idEmployee}', function($idEmployee){
    return Ctrl\Employee::delete($idEmployee);
});

/* rotas de time */

$router->addGetRoute('ponto', function(Request $request){
    return Ctrl\Time::list($request);
});

$router->addGetRoute('ponto/form', function(){
    return Ctrl\Time::form();
});

$router->addPostRoute('ponto/registrar', function(Request $request){
    return Ctrl\Time::register($request);
});

$router->addGetRoute('ponto/atualizar/{idTime}', function($idTime){
    return Ctrl\Time::form($idTime);
});

$router->addGetRoute('ponto/delete/{idTime}', function($idTime){
    return Ctrl\Time::delete($idTime);
});