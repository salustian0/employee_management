<?php

namespace App\Controllers;

use App\Entity\EmployeeEntity;
use App\Models\EmployeeModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Security;
use App\system\Utils\Session;
use App\Views\View;

class Employee
{

    static function form($idEmployee = null)
    {
        Login::verifyAuth();


        /**
         * Valida id do funcionário caso informado
         */
        if ($idEmployee !== null && !Security::validateInt($idEmployee)) {
            $message = array("error" => array("id informado incorretamente"));
            (new Response())->redirect('/funcionarios', $message);
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
        if (is_null($idEmployee)) {
            $vars['title'] = 'Registro de funcionário';
        } else {
            /**
             * Configurações formulário de atualização
             */
            $vars['title'] = 'Atualização do funcionário';
            $vars['id'] = $idEmployee;
            /**
             * Buscando funcionário por id
             */

            $userModel = new EmployeeModel();
            $employeeEntity = $userModel->getEmployeeById($idEmployee);

            if (empty($employeeEntity)) {
                $message = array(
                    "error" => array(
                        "funcionário inexistente"
                    )
                );
                return (new Response())->redirect('/funcionarios', $message);
            }

            /**
             * setando variáveis
             */
            if (empty($vars['_data'])) {
                $vars['_data'] = array(
                    'name' => $employeeEntity->getName(),
                    'email' => $employeeEntity->getEmail(),
                    'cpf' => $employeeEntity->getCpf(),
                    'office' => $employeeEntity->getOffice()
                );
            }
        }

        /**
         * render view
         */
        $view->render('employee/form', $vars);
    }


    static function list()
    {
        Login::verifyAuth();

        $vars = [];
        $view = new View();
        $employeeModel = new EmployeeModel();

        /**
         * Busca todos os usuários
         */
        $arrEmployeeEntity = $employeeModel->getAllEmployees();

        /**
         * Setando variáveis para a view
         */
        $vars['list'] = $arrEmployeeEntity ?? [];
        $vars['title'] = 'Listagem de funcionários';

        /**
         * render view
         */
        $view->setJsFile('list.js');
        $view->setJsVar('const','MODULE', 'funcionarios');
        $view->render('employee/list', $vars);
    }

    /**
     * Registro de usuário
     * @param Request $request
     */
    static function register(Request $request)
    {
        Login::verifyAuth();

        $response = new Response();
        /**
         * id (caso seja update)
         */
        $idEmployee = $request->getPostParams('id');

        /**
         * Setando dados na entidade
         */
        $employeeEntity = new EmployeeEntity();
        $employeeEntity->setName($request->getPostParams('name'));
        $employeeEntity->setEmail($request->getPostParams('email'));
        $employeeEntity->setCpf($request->getPostParams('cpf'));
        $employeeEntity->setOffice($request->getPostParams('office'));


        /**
         * Instancia da model
         */
        $employeeModel = new EmployeeModel();
        /**
         * Update
         */
        if (!empty($idEmployee)) {
            /**
             * Dados que precisam ser setados na ação de update
             */
            $employeeEntity->setId($idEmployee); //id define qual o id do registro deve ser alterado
            $employeeEntity->setUpdatedAt(date('Y-m-d H:i:s'));

            /**
             * Validação da ação de update
             */
            $errors = self::validateUpdate($employeeEntity);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect("/funcionarios/atualizar/{$idEmployee}", $messages, $request->getPostParams());
            }

            /**
             * Realizando update
             */
            if ($employeeModel->updateEmployee($employeeEntity)) {
                $messages = array('success' => array('Funcionário alterado com sucesso'));
                return $response->redirect("/funcionarios", $messages);
            } else {
                $messages = array('error' => array('Houve um erro durante a tentativa de atualização do funcionário'));
                return $response->redirect("/funcionarios/atualizar/{$idEmployee}", $messages, $request->getPostParams());
            }

        } else {
            /**
             * Insert
             */

            /**
             * Validando ação de registro
             */
            $errors = self::validateRegister($employeeEntity);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect('/funcionarios/form', $messages, $request->getPostParams());
            }

            /**
             * Realizando a inclusão de funcionário
             */
            if ($employeeModel->addEmployee($employeeEntity)) {
                $messages = array("success" => array("funcionário criado com sucesso!"));
                return $response->redirect('/funcionarios', $messages);
            } else {
                $messages = array("error" => array("houve um erro na tentativa de registro do funcionário"));
                return $response->redirect('/funcionarios/form', $messages, $request->getPostParams());
            }
        }
    }


    /**
     * @param EmployeeEntity $employeeEntity
     * @return array
     */
    private static function validateRegister(EmployeeEntity $employeeEntity)
    {
        Login::verifyAuth();

        $errors = array();

        if (empty($employeeEntity->getName())) {
            array_push($errors, 'O nome de funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getName()) < 3 || mb_strlen($employeeEntity->getName()) > 150) {
                array_push($errors, 'O nome do funcionário  deve conter entre 3 e 150 caracteres');
            }
        }

        if (empty($employeeEntity->getCpf())) {
            array_push($errors, 'O cpf do funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getCpf()) != 11) {
                array_push($errors, 'O cpf deve conter 11 caracteres');
            }else if((new EmployeeModel)->hasByCpf($employeeEntity->getCpf())){
                array_push($errors, 'Cpf já existente na base de dados');
            }
        }

        if (empty($employeeEntity->getEmail())) {
            array_push($errors, 'O email do funcionário é obrigatório');
        } else {
            $email = filter_var($employeeEntity->getEmail(), FILTER_VALIDATE_EMAIL);
            if ($email == false) {
                array_push($errors, 'O email informado é inválido');
            }else if((new EmployeeModel)->hasByEmail($employeeEntity->getEmail())){
                array_push($errors, 'Email já existente na base de dados');
            }
        }

        if (empty($employeeEntity->getOffice())) {
            array_push($errors, 'O cargo do funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getOffice()) < 3 || mb_strlen($employeeEntity->getOffice()) > 150) {
                array_push($errors, 'O cargo do funcionário  deve conter entre 3 e 150 caracteres');
            }
        }

        return $errors;
    }

    /**
     * @param EmployeeEntity $employeeEntity
     * @return array
     */
    private static function validateUpdate(EmployeeEntity  $employeeEntity)
    {
        Login::verifyAuth();

        $errors = array();


        if (empty($employeeEntity->getName())) {
            array_push($errors, 'O nome de funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getName()) < 3 || mb_strlen($employeeEntity->getName()) > 150) {
                array_push($errors, 'O nome do funcionário  deve conter entre 3 e 150 caracteres');
            }
        }

        if (empty($employeeEntity->getCpf())) {
            array_push($errors, 'O cpf do funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getCpf()) != 11) {
                array_push($errors, 'O cpf deve conter 11 caracteres');
            }else if((new EmployeeModel)->hasByCpf($employeeEntity->getCpf(), $employeeEntity->getId())){
                array_push($errors, 'Cpf já existente na base de dados');
            }
        }

        if (empty($employeeEntity->getEmail())) {
            array_push($errors, 'O email do funcionário é obrigatório');
        } else {
            $email = filter_var($employeeEntity->getEmail(), FILTER_VALIDATE_EMAIL);
            if ($email == false) {
                array_push($errors, 'O email informado é inválido');
            }else if((new EmployeeModel)->hasByEmail($employeeEntity->getEmail(), $employeeEntity->getId())){
                array_push($errors, 'Email já existente na base de dados');
            }
        }

        if (empty($employeeEntity->getOffice())) {
            array_push($errors, 'O cargo do funcionário é obrigatório');
        } else {
            if (mb_strlen($employeeEntity->getOffice()) < 3 || mb_strlen($employeeEntity->getOffice()) > 150) {
                array_push($errors, 'O cargo do funcionário  deve conter entre 3 e 150 caracteres');
            }
        }

        return $errors;
    }

    /**
     * Método responsável por excluir um usuário
     * @param $idUser
     */
    static function delete($idEmployee)
    {
        Login::verifyAuth();

        $response = new Response();
        /**
         * Valida o parâmetro informado
         */
        if (!Security::validateInt($idEmployee)) {
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/funcionarios', $message);
        }

        /**
         * Instancia da model
         */
        $employeeModel = new EmployeeModel();
        /**
         * Valida existencia do registro
         */
        $userExists = $employeeModel->hasById($idEmployee);
        if ($userExists) {
            /**
             * Realiza a exclusão
             */
            if ($employeeModel->delete($idEmployee)) {
                $message = array('success' => array('Funcionário removido com sucesso'));
                return $response->redirect('/funcionarios', $message);
            }
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/funcionarios', $message);
        }

        $message = array('error' => array('Funcionário inexistente'));
        return $response->redirect('/funcionarios', $message);
    }
}