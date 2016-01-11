<?php

namespace Api\Controller;

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
                'id' => $user->getUserId(),
                'email' => $user->getEmail(),
                'name' => $user->getDisplayName()
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
        $response = array();
        $email = $this->params()->fromPost('email');
        $pass = $this->params()->fromPost('password');
        $service = $this->getUserService();
        if (!is_null($email) && !is_null($pass)) {
            try {
                $serviceResponse = $service->userLogin($email, $pass);
                $response = array(
                    'status' => true,
                    'data' => $serviceResponse,
                    'message' => ''
                );
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'exception' => 'INVALID_USER',
                    'data' => null,
                    'message' => $e->getMessage()
                );
            }

        } else {
            $response = array(
                'status' => false,
                'exception' => 'INVALID_DATA',
                'data' => null,
                'message' => 'Please Provide valid Email and Password'
            );

        }
        return new JsonModel($response);
    }

    public function registerAction()
    {
        $response = array();
        $service = $this->getUserService();
        $request = $this->getRequest();
        $userForm = new UserForm();
        $userModel = $this->getServiceLocator()->get('api_user_entity');
        $userModel->setData($request->getPost());
        $userForm->setInputFilter($userModel->inputFilter());
        $userForm->setData($request->getPost());
        if ($userForm->isValid()) {
            try {
                $userModel->exchangeArray($userForm->getData());
                $model = $service->save($userModel);
                $response = array(
                    'status' => true,
                    'data' => $model,
                    'message' => ''
                );
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'exception' => 'SERVICE_ERROR',
                    'data' => null,
                    'message' => $e->getMessage()
                );
            }
        } else {
            $response = array(
                'status' => false,
                'exception' => 'INVALID_DATA',
                'data' => $userForm->getMessages(),
                'message' => 'Please Provide valid data'
            );
        }
        return new JsonModel($response);
    }
}