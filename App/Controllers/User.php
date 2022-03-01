<?php

namespace App\Controllers;

use App\Entity\UserEntity;
use App\Models\UserModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Security;
use App\system\Utils\Session;
use App\Views\View;

class User
{

    static function form($idUser = null)
    {

        /**
         * Valida id do usuário caso informado
         */
        if ($idUser !== null && !Security::validateInt($idUser)) {
            $message = array(
                "error" => array(
                    "id informado incorretamente"
                )
            );
            (new Response())->redirect('/usuarios', $message);
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
        if (is_null($idUser)) {
            $vars['title'] = 'Registro de usuário';
        } else {
            /**
             * Configurações formulário de atualização
             */
            $vars['title'] = 'Atualização de usuário';
            $vars['id'] = $idUser;
            /**
             * Buscando usuário por id
             */

            $userModel = new UserModel();
            $userEntity = $userModel->getUserById($idUser);

            if (empty($userEntity)) {
                $message = array(
                    "error" => array(
                        "Usuário inexistente"
                    )
                );
                return (new Response())->redirect('/usuarios', $message);
            }

            /**
             * setando variáveis
             */
            if (empty($vars['_data'])) {
                $vars['_data'] = array(
                    'username' => $userEntity->getUsername(),
                    'access' => $userEntity->getAccess()
                );
            }
        }

        /**
         * render view
         */
        $view->render('user/form', $vars);
    }


    static function list()
    {
        $vars = [];
        $view = new View();
        $userModel = new UserModel();

        /**
         * Busca todos os usuários
         */
        $arrUserEntity = $userModel->getAllUsers();

        /**
         * Setando variáveis para a view
         */
        $vars['list'] = $arrUserEntity ?? [];
        $vars['title'] = 'Listagem de usuários';

        /**
         * render view
         */
        $view->setJsFile('user/list.js');
        $view->render('user/list', $vars);
    }

    /**
     * Registro de usuário
     * @param Request $request
     */
    static function register(Request $request)
    {
        $response = new Response();
        /**
         * id (caso seja update)
         */
        $idUser = $request->getPostParams('id');

        /**
         * Setando dados na entidade
         */
        $userEntity = new UserEntity();
        $userEntity->setUsername($request->getPostParams('username'));
        $userEntity->setPassword($request->getPostParams('password'));
        $userEntity->setAccess($request->getPostParams('access'));
        /**
         * outros paramêtros não presentes na entidade que precisam ser validados
         */
        $options['confirm_password'] = $request->getPostParams('confirm_password');

        /**
         * Instancia da model
         */
        $userModel = new UserModel();
        /**
         * Update
         */
        if (!empty($idUser)) {
            /**
             * Dados que precisam ser setados na ação de update
             */
            $userEntity->setId($idUser); //id define qual o id do registro deve ser alterado
            $userEntity->setUpdatedAt(date('Y-m-d H:i:s'));

            /**
             * Validação da ação de update
             */
            $errors = self::validateUpdate($userEntity, $options);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect("/usuarios/atualizar/{$idUser}", $messages, $request->getPostParams());
            }

            /**
             * Realizando update
             */
            if ($userModel->updateUser($userEntity)) {
                $messages = array('success' => array('Usuário alterado com sucesso'));

                return $response->redirect("/usuarios", $messages, $request->getPostParams());
            } else {
                $messages = array('error' => array('Houve um erro durante a tentativa de atualização do usuário'));
                return $response->redirect("/usuarios/atualizar/{$idUser}", $messages, $request->getPostParams());
            }

        } else {
            /**
             * Insert
             */

            /**
             * Validando ação de registro
             */
            $errors = self::validateRegister($userEntity, $options);
            if (!empty($errors)) {
                $messages = array("error" => $errors);
                return $response->redirect('/usuarios/form', $messages, $request->getPostParams());
            }

            /**
             * Realizando a inclusão de usuario
             */
            if ($userModel->addUser($userEntity)) {
                $messages = array("success" => array("usuário criado com sucesso!"));
                return $response->redirect('/usuarios', $messages);
            } else {
                $messages = array("error" => array("houve um erro na tentativa de registro do usuário"));
                return $response->redirect('/usuarios/form', $messages, $request->getPostParams());
            }
        }
    }


    /**
     * @param UserEntity $userEntity
     * @param array $options
     * @return array
     */
    private static function validateRegister(UserEntity $userEntity, array $options)
    {
        $errors = array();

        if ($userEntity->getUsername() === null) {
            array_push($errors, 'O nome de usuário é obrigatório');
        } else {
            if (mb_strlen($userEntity->getUsername()) < 3 || mb_strlen($userEntity->getUsername()) > 18) {
                array_push($errors, 'O nome de usuário deve conter entre 3 e 18');
            }else if((new UserModel())->hasByUsername($userEntity->getUsername())){
                array_push($errors, 'Usuário já existente na base de dados, por favor tente um usuário diferente!');
            }
        }

        if ($userEntity->getPassword() === null) {
            array_push($errors, 'A senha é obrigatória');
        } else {
            if (empty($options['confirm_password'])) {
                array_push($errors, 'O campo confirmar senha é obrigatório');
            } else if ($options['confirm_password'] != $userEntity->getPassword()) {
                array_push($errors, 'As senhas não coincidem');
            }
        }

        if (!in_array($userEntity->getAccess(), ['USER', 'ADM'])) {
            array_push($errors, 'O tipo de acesso é inválido');
        }

        return $errors;
    }

    /**
     * @param UserEntity $userEntity
     * @param array $options
     * @return array
     */
    private static function validateUpdate(UserEntity $userEntity, array $options)
    {
        $errors = array();

        if ($userEntity->getUsername() === null) {
            array_push($errors, 'O nome de usuário é obrigatório');
        } else {
            if (mb_strlen($userEntity->getUsername()) < 3 || mb_strlen($userEntity->getUsername()) > 18) {
                array_push($errors, 'O nome de usuário deve conter entre 3 e 18');
            }else if((new UserModel())->hasByUsername($userEntity->getUsername(), $userEntity->getId())){
                array_push($errors, 'Usuário já existente no banco de dados');
            }
        }

        if ($userEntity->getPassword() !== null) {
            if (empty($options['confirm_password'])) {
                array_push($errors, 'O campo confirmar senha é obrigatório');
            } else if ($options['confirm_password'] != $userEntity->getPassword()) {
                array_push($errors, 'As senhas não coincidem');
            }
        }

        if (!in_array($userEntity->getAccess(), ['USER', 'ADM'])) {
            array_push($errors, 'O tipo de acesso é inválido');
        }

        return $errors;
    }

    /**
     * Método responsável por excluir um usuário
     * @param $idUser
     */
    static function delete($idUser)
    {
        $response = new Response();
        /**
         * Valida o parâmetro informado
         */
        if (!Security::validateInt($idUser)) {
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/usuarios', $message);
        }

        /**
         * Instancia da model
         */
        $userModel = new UserModel();
        /**
         * Valida existencia do registro
         */
        $userExists = $userModel->hasById($idUser);
        if ($userExists) {
            /**
             * Realiza a exclusão
             */
            if ($userModel->delete($idUser)) {
                $message = array('success' => array('Usuário removido com sucesso'));
                return $response->redirect('/usuarios', $message);
            }
            $message = array('error' => array('Houve um erro durante a tentativa de exclusão do registro'));
            return $response->redirect('/usuarios', $message);
        }

        $message = array('error' => array('Usuário inexistente'));
        return $response->redirect('/usuarios', $message);
    }
}