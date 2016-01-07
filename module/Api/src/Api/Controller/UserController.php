<?php

namespace Api\Controller;

use Api\Entity\User;
use Api\Form\UserForm;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class UserController extends AbstractRestfulController
{

    private $userService;

    private function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('api_user_service');
        }
        return $this->userService;
    }

    public function indexAction()
    {
        $users = array();
        foreach ($this->getUserService()->getUsersList() as $user) {
            $users[] = array(
                'email' => $user->getEmail()
            );
        }
        return new JsonModel(
            array(
                'users' => $users
            )
        );
    }

    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!is_null($id)) {
            return new JsonModel(array(
                'member' => $this->getUserService()->getUser($id)->getEmail()
            ));
        }
        throw new \Exception('invalid params');
    }

    public function userLoginAction()
    {

        $email = $this->params()->fromPost('email');
        $pass = $this->params()->fromPost('password');
        $service = $this->getUserService();
        if (!is_null($email) && !is_null($pass)) {
            return new JsonModel(array(
                'isValidUser' => $service->userLogin($email, $pass)
            ));
        }
        throw new \Exception('invalid arguments for user login');
    }

    public function registerAction()
    {
        $service = $this->getUserService();
        $request = $this->getRequest();
        $userForm = new UserForm();
        $userModel = $this->getServiceLocator()->get('api_user_entity');
        $userModel->setData($request->getPost());
        $userForm->setInputFilter($userModel->inputFilter());
        $userForm->setData($request->getPost());
        if ($userForm->isValid()) {
            $userModel->exchangeArray($userForm->getData());
            $model = $service->save($userModel);
            return new JsonModel(
                array(
                    'data' => $model
                )
            );
        } else {
            throw new \Exception('Please Provide valid data and try again.');
        }
        throw new \Exception('An error occurred during execution; please try again later.');

    }
}