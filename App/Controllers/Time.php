<?php

namespace App\Controllers;
use App\Entity\TimeEntity;
use App\Models\EmployeeModel;
use App\Models\TimeModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Security;
use App\system\Utils\Session;
use App\Views\View;

class Time
{

    static function form($idTime = null)
    {
        Login::verifyAuth();

        /**
         * Valida id do time caso informado
         */
        if ($idTime !== null && !Security::validateInt($idTime)) {
            $message = array("error" => array("id informado incorretamente"));
            (new Response())->redirect('/time', $message);
        }

        $view = new View();
        $vars = [];
        $oldData = Session::getFlashData('oldData');
        if (!empty($oldData)) {
            $vars['_data'] = $oldData;
        }

        /**
         * Configurações formulário de registro
         */
        if (is_null($idTime)) {
            $vars['title'] = 'Registro de time';
        } else {
            /**
             * Configurações formulário de atualização
             */
            $vars['title'] = 'Atualização do time';
            $vars['id'] = $idTime;
            /**
             * Buscando time por id
             */

            $timeModel = new TimeModel();
            $timeEntity = $timeModel->getTimeById($idTime);

            if (empty($timeEntity)) {
                $message = array(
                    "error" => array(
                        "registro de time inexistente"
                    )
                );
                return (new Response())->redirect('/time', $message);
            }

            /**
             * setando variáveis
             */
            if (empty($vars['_data'])) {
                $vars['_data'] = array(
                    'start' => $timeEntity->getStart(),
                    'end' => $timeEntity->getEnd(),
                    'date' => $timeEntity->getDate(),
                );
            }
        }

        $employeeModel = new EmployeeModel();
        $arrEmployeeEntity = $employeeModel->getAllEmployees();
        $vars['list_employees'] = $arrEmployeeEntity ?? [];

        /**
         * render view
         */
        $view->render('time/form', $vars);
    }


    static function list(Request $request)
    {
        Login::verifyAuth();

        $vars = [];
        $view = new View();
        $timeModel = new TimeModel();
        /**
         * Busca todos os pontos
         */

        $filters = array();
        if($request->getGetParams('filter')){
            $vars['filter'] = true;
            $period_start = $request->getGetParams('period_start');
            $period_end = $request->getGetParams('period_end');
            $order_name = $request->getGetParams('order_name');
            $order_date = $request->getGetParams('order_date');

            if(!empty($period_start)){
                $vars['period_start'] = $period_start;
                $filters['period_start'] = $period_start;
            }

            if(!empty($period_end)){
                $vars['period_end'] = $period_end;
                $filters['period_end'] = $period_end;
            }

            if(!empty($order_name) && in_array($order_name, ['ASC','DESC'])){
                $vars['order_name'] = $order_name;
                $filters['order_name'] = $order_name;
            }

            if(!empty($order_date) && in_array($order_date, ['ASC','DESC'])){
                $filters['order_date'] = $order_date;
                $vars['order_date'] = $order_date;
            }
        }

        $arrTimeEntity = $timeModel->getAllTime($filters);
        $employeeModel = new EmployeeModel();
        $arrEmployeeEntity = $employeeModel->getAllEmployees();
        /**
         * Setando variáveis para a view
         */
        $vars['list'] = $arrTimeEntity ?? [];
        $vars['list_employees'] = $arrEmployeeEntity ?? [];

        $vars['title'] = 'Listagem de pontos de funcionários';

        /**
         * render view
         */
        $view->setJsFile('list.js');
        $view->setJsVar('const','MODULE', 'ponto');
        $view->render('time/list', $vars);
    }

    /**
     * Registro de time
     * @param Request $request
     */
    static function register(Request $request)
    {
        Login::verifyAuth();

        $response = new Response();
        /**
         * id (caso seja update)
         */
        $idTime = $request->getPostParams('id');

        /**
         * Setando dados na entidade
         */
        $timeEntity = new TimeEntity();
        $timeEntity->setStart($request->getPostParams('start'));
        $timeEntity->setEnd($request->getPostParams('end'));
        $timeEntity->setDate($request->getPostParams('date'));
        $timeEntity->setEmployeeId($request->getPostParams('employee_id'));


        /**
         * Instancia da model
         */
        $timeModel = new TimeModel();
        /**
         * Update
         */
        if (!empty($idTime)) {
            /**
             * Dados que precisam ser setados na ação de update
             */
            $timeEntity->setId($idTime); //id define qual o id do registro deve ser alterado
            $timeEntity->setUpdatedAt(date('Y-m-d H:i:s'));

            /**
             * Validação da ação de update
             */
            $errors = self::validateUpdate($timeEntity);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect("/ponto/atualizar/{$idTime}", $messages, $request->getPostParams());
            }

            /**
             * Realizando update
             */
            if ($timeModel->updateTime($timeEntity)) {
                $messages = array('success' => array('Ponto alterado com sucesso'));
                return $response->redirect("/ponto", $messages);
            } else {
                $messages = array('error' => array('Houve um erro durante a tentativa de atualização do time'));
                return $response->redirect("/ponto/atualizar/{$idTime}", $messages, $request->getPostParams());
            }

        } else {
            /**
             * Insert
             */

            /**
             * Validando ação de registro
             */
            $errors = self::validateRegister($timeEntity);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect('/ponto/form', $messages, $request->getPostParams());
            }

            /**
             * Realizando a inclusão de time
             */
            if ($timeModel->addTime($timeEntity)) {
                $messages = array("success" => array("time registrado com sucesso!"));
                return $response->redirect('/ponto', $messages);
            } else {
                $messages = array("error" => array("houve um erro na tentativa de registro de ponto"));
                return $response->redirect('/ponto/form', $messages, $request->getPostParams());
            }
        }
    }


    /**
     * @param TimeEntity $timeEntity
     * @return array
     */
    private static function validateRegister(TimeEntity $timeEntity)
    {
        Login::verifyAuth();
        $errors = array();

        if(empty($timeEntity->getStart())){
            array_push($errors, 'O campo hora inicial é obrigatório');
        }else{
            $dateObj = \DateTime::createFromFormat('H:i', $timeEntity->getStart());
            if(!($dateObj && $dateObj->format('H:i') == $timeEntity->getStart())){
                array_push($errors, 'o campo hora inicial é inválido');
            }
        }

        if(!empty($timeEntity->getStart())){
            $dateObj = \DateTime::createFromFormat('H:i', $timeEntity->getEnd());
            if(!($dateObj && $dateObj->format('H:i') == $timeEntity->getEnd())){
                array_push($errors, 'o campo hora final é inválido');
            }
        }

        if(empty($timeEntity->getDate())){
            array_push($errors, 'o campo data é obrigatório');
        }else{
            $dateObj = \DateTime::createFromFormat('Y-m-d', $timeEntity->getDate());
            if(!($dateObj && $dateObj->format('Y-m-d') == $timeEntity->getDate())){
                array_push($errors, 'o campo data informado é inválido');
            }
        }


        return $errors;
    }

    /**
     * @param TimeEntity $timeEntity
     * @return array
     */
    private static function validateUpdate(TimeEntity  $timeEntity)
    {
        Login::verifyAuth();

        $errors = array();

        return $errors;
    }

    /**
     * Método responsável por excluir um time
     * @param $idTime
     */
    static function delete($idTime)
    {
        Login::verifyAuth();

        $response = new Response();
        /**
         * Valida o parâmetro informado
         */
        if (!Security::validateInt($idTime)) {
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/ponto', $message);
        }

        /**
         * Instancia da model
         */
        $timeModel = new TimeModel();
        /**
         * Valida existencia do registro
         */
        $timeExists = $timeModel->hasById($idTime);
        if ($timeExists) {
            /**
             * Realiza a exclusão
             */
            if ($timeModel->delete($idTime)) {
                $message = array('success' => array('registro de ponto removido com sucesso'));
                return $response->redirect('/ponto', $message);
            }
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/ponto', $message);
        }

        $message = array('error' => array('Registro de ponto  inexistente'));
        return $response->redirect('/ponto', $message);
    }
}