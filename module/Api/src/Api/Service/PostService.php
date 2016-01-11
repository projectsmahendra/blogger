<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 11/1/16
 * Time: 3:00 PM
 */

namespace Api\Service;


use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class PostService implements ServiceManagerAwareInterface
{
    private $serviceManager = null;
    private $hydrator = null;
    private $protoType = null;
    private $adaptor = null;

    public function getPostList()
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select(array(
            'p' => 'posts'
        ));
        $select->join(array(
            'u' => 'user'
        ), 'p.author_id=u.user_id', array('author' => 'display_name'));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet($this->getHydrator(), $this->getProtoType());
            return $resultSet->initialize($result);
        }
        return array();
    }

    public function getPost($id)
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select(array(
            'p' => 'posts'
        ));
        $select->where(array(
            'id = ?' => $id
        ));
        $select->join(array(
            'u' => 'user'
        ), 'p.author_id=u.user_id', array('author' => 'display_name'));
        $statment = $sql->prepareStatementForSqlObject($select);
        $result = $statment->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $result->current();
        }
        throw new \InvalidArgumentException("Post not found with given ID:{$id} not found.");
    }


    public function save(\Api\Entity\Post $post)
    {
        $hydrator = $this->getHydrator();
        $action = null;
        $postData = array(
            'title' => $post->getTitle(),
            'description' => $post->getDescription(),
        );
        if ($post->getId()) {
            $action = new Update('posts');
            $action->set($postData);
            $action->where(array(
                'id = ?' => $post->getId()
            ));
        } else {
            $postData['author_id'] = $post->getAuthorId();
            $action = new Insert('posts');
            $action->values($postData);
        }
        $sql = new Sql($this->getAdaptor());
        $statement = $sql->prepareStatementForSqlObject($action);
        $result = $statement->execute();
        if ($result instanceof ResultInterface) {
            if ($pk = $result->getGeneratedValue()) {
                $post->setId($pk);
            }
            return $this->getPost($post->getId());
        }
        throw new \Exception('something went wrong.Please try again later');
    }


    private function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods();
        }
        return $this->hydrator;
    }

    private function getProtoType()
    {
        if (!$this->protoType) {
            $this->protoType = new \Api\Entity\Post();
        }
        return $this->protoType;
    }

    private function getAdaptor()
    {
        if (!$this->adaptor) {
            $this->adaptor = $this->serviceManager->get('Zend\Db\Adapter\Adapter');
        }
        return $this->adaptor;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        if (!$this->serviceManager) {
            $this->serviceManager = $serviceManager;
        }
        return $this->serviceManager;
    }
}