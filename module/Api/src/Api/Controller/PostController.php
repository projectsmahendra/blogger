<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 11/1/16
 * Time: 2:57 PM
 */

namespace Api\Controller;


use Api\Entity\Post;
use Api\Form\PostForm;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class PostController extends AbstractRestfulController
{
    private $postService;


    public function indexAction()
    {
        $response = array();
        try {
            $posts = array();
            foreach ($this->getPostService()->getPostList() as $post) {
                $posts[] = array(
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'description' => $post->getDescription(),
                    'author' => $post->getAuthor()
                );
            }
            $response = array(
                'status' => true,
                'data' => $posts,
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
        return new JsonModel($response);
    }

    public function viewAction()
    {
        $response = array();
        $id = $this->params()->fromRoute('id');
        if (!is_null($id)) {
            try {
                $post = $this->getPostService()->getPost($id);
                $response = array(
                    'status' => true,
                    'data' => $post,
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
                'data' => null,
                'message' => 'Please Provide valid post info'
            );
        }
        return new JsonModel($response);
    }

    public function userLoginAction()
    {
        $response = array();
        $email = $this->params()->fromPost('email');
        $pass = $this->params()->fromPost('password');
        $service = $this->getPostService();
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

    public function addAction()
    {
        $response = array();
        $service = $this->getPostService();
        $request = $this->getRequest();
        $postForm = new PostForm();
        $userModel = new Post();
        $postForm->setInputFilter($userModel->inputFilter());
        $postForm->setData($request->getPost());
        if ($postForm->isValid()) {
            try {
                $userModel->exchangeArray($postForm->getData());
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
                'data' => $postForm->getMessages(),
                'message' => 'Please Provide valid data'
            );
        }
        return new JsonModel($response);
    }

    private function getPostService()
    {
        if (!$this->postService) {
            $this->postService = $this->getServiceLocator()->get('api_post_service');
        }
        return $this->postService;
    }
}