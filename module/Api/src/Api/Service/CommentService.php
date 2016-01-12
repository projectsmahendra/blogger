<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 12/1/16
 * Time: 10:37 AM
 */

namespace Api\Service;


use Api\Entity\Comment;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;


class CommentService implements ServiceManagerAwareInterface
{
    private $serviceManager = null;
    private $hydrator = null;
    private $protoType = null;
    private $adaptor = null;

    public function getCommentList($postId, $page, $limit)
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select(array(
            'p' => 'comments'
        ));
        $select->join(array(
            'u' => 'user'
        ), 'p.author_id=u.user_id', array('author' => 'display_name'));
        $select->where(array(
            'post_id = ?' => $postId
        ));
        $select->limit(intval($limit));
        $select->offset(intval($limit) * (intval($page) - 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet($this->getHydrator(), $this->getProtoType());
            return $resultSet->initialize($result);
        }
        return array();
    }

    public function getComment($id)
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select(array(
            'p' => 'comments'
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
        throw new \InvalidArgumentException("Comment not found with given ID:{$id} not found.");
    }

    public function save(Comment $comment)
    {
        $hydrator = $this->getHydrator();
        $hydrator->addFilter(
            "inputFilter",
            new MethodMatchFilter("getInputFilter"),
            FilterComposite::CONDITION_AND
        );
        $hydrator->addFilter(
            "array_copy",
            new MethodMatchFilter("getArrayCopy"),
            FilterComposite::CONDITION_AND
        );
        $hydrator->addFilter(
            "getAuthor",
            new MethodMatchFilter("getAuthor"),
            FilterComposite::CONDITION_AND
        );
        $postData = $hydrator->extract($comment);
        $insert = new Insert('comments');
        $insert->values($postData);
        $sql = new Sql($this->getAdaptor());
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        if ($result instanceof ResultInterface) {
            if ($pk = $result->getGeneratedValue()) {
                $comment->setId($pk);
            }
            return $this->getComment($comment->getId());
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
            $this->protoType = new \Api\Entity\Comment();
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