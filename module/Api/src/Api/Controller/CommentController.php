<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 12/1/16
 * Time: 10:25 AM
 */

namespace Api\Controller;


use Api\Entity\Comment;
use Api\Form\CommentForm;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class CommentController extends AbstractRestfulController
{
    private $commentService;


    public function indexAction()
    {
        $response = array();
        $postId = $this->params()->fromPost('postId');
        $page = $this->params()->fromPost('page', 1);
        $limit = $this->params()->fromPost('limit', 10);
        if (!is_null($postId)) {
            try {
                $comments = array();
                foreach ($this->getCommentService()->getCommentList($postId, $page, $limit) as $comment) {
                    $comments[] = array(
                        'id' => $comment->getId(),
                        'description' => $comment->getDescription(),
                        'author' => $comment->getAuthor()
                    );
                }
                $response = array(
                    'status' => true,
                    'data' => $comments,
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
                'exception' => 'INVALID_DATA_ERROR',
                'data' => null,
                'message' => 'Please Provide post id'
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
                $comment = $this->getCommentService()->getComment($id);
                $response = array(
                    'status' => true,
                    'data' => $comment,
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


    public function addAction()
    {
        $response = array();
        $service = $this->getCommentService();
        $request = $this->getRequest();
        $form = new CommentForm();
        $model = new Comment();
        $form->setInputFilter($model->getInputFilter());
        $form->setData($request->getPost());
        if ($form->isValid()) {
            try {
                $model->exchangeArray($form->getData());
                $model = $service->save($model);
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
                'data' => $form->getMessages(),
                'message' => 'Please Provide valid data'
            );
        }
        return new JsonModel($response);
    }

    private function getCommentService()
    {
        if (!$this->commentService) {
            $this->commentService = $this->getServiceLocator()->get('api_comment_service');
        }
        return $this->commentService;
    }
}