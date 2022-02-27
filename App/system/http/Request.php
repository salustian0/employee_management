<?php
namespace  App\system\http;
use const App\Config\DEFAULT_ACTION;
use const App\Config\DEFAULT_CONTROLLER;

/**
 * Classe responsável pelo gerenciamento das requisições
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 */
class Request{

    private array $getParams;
    private array $postParams;
    private array $headers;
    private string $requestedUri;
    private string $controller;
    private string $action;
    private array $params;
    private string $requestedMethod;

    public function __construct()
    {
        $this->requestedUri = $this->getUri();

        $this->getParams = $_GET ?? [];
        $this->postParams = $_POST ?? [];
        $this->requestedMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $this->headers = getallheaders();
    }

    /**
     * Método responsável por retornar a url da requisição
     * @return string
     */
    private function getUri() : string {
        $uri = "/";
        if(isset($_GET['uri'])){
            $uri = filter_input(INPUT_GET, 'uri', FILTER_DEFAULT);
            unset($_GET['uri']);
        }
        return $uri;
    }
//
//    /**
//     * Método responsável por explodir a url requisitada
//     * @return array
//     */
//    private function explodeUri() : array{
//        $explodedUri = explode('/', $this->requestedUri);
//        $explodedUri = array_filter($explodedUri);
//        return $explodedUri;
//    }
//
//    /**
//     * Método responsável por setar argumentos necessários: Controller/Action/[Params]
//     * @param array $explodedUri
//     */
//    private function setRequiredArgs(array $explodedUri){
//
//        if(empty($explodedUri[1])){
//            $explodedUri[1] = DEFAULT_ACTION;
//        }
//
//        list($this->controller, $this->action) = $explodedUri;
//        if(count($explodedUri) > 2){
//            unset($explodedUri[0]);
//            unset($explodedUri[1]);
//            $this->params = array_values($explodedUri);
//        }
//    }

    /**
     * Método responsável por retornar todos ou 1 parâmetros especifico do tipo GET
     * @param string $paramName chave do parâmetro GET especifico
     * @return mixed|null
     */
    public function getGetParams(string $paramName = null){
        return $this->getParams[$paramName] ?? $this->getParams;
    }

    /**
     * Método responsável por retornar todos ou 1 parâmetros especifico do tipo POST
     * @param string $paramName chave do parâmetro POST especifico
     * @return mixed|null
     */
    public function getPostParams(string $paramName = null){
        return $this->postParams[$paramName] ?? $this->postParams;
    }


    public function getRequestedUri() : string{
        return $this->requestedUri;
    }

    public function getRequestedMethod(){
        return $this->requestedMethod;
    }
}