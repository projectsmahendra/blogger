<?php

namespace Api\Service;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserService implements ServiceManagerAwareInterface
{
    private $serviceManager = null;
    private $hydrator = null;
    private $protoType = null;
    private $adaptor = null;

    public function getUsersList()
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select('user');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet($this->getHydrator(), $this->getProtoType());
            return $resultSet->initialize($result);
        }
        return array();
    }

    public function getUser($id)
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select('user');
        $select->where(array(
            'user_id = ?' => $id
        ));
        $statment = $sql->prepareStatementForSqlObject($select);
        $result = $statment->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $result->current();
        }
        throw new \InvalidArgumentException("Member not found with given ID:{$id} not found.");
    }

    public function userLogin($email, $pass)
    {
        $sql = new Sql($this->getAdaptor());
        $select = $sql->select('user');
        $criteria = new Where();
        $criteria->equalTo('password', md5($pass));
        $criteria->equalTo('email', $email);
        $criteria->OR->equalTo('username', $email);
        $select->where($criteria);
        $statment = $sql->prepareStatementForSqlObject($select);
        $result = $statment->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return array(
                'isValidUser' => true,
                'user' => $result->current()
            );
        }
        throw new \InvalidArgumentException("Member with given email :{$email} not found.");
    }

    public function save(\Api\Entity\User $user)
    {
        $hydrator = $this->getHydrator();
        $postData = array(
            'display_name' => $user->getEmail(),
            'password' => $user->getPassword(),
        );
        $postData = array_merge($postData, array(
            'email' => $user->getEmail(),
            'username' => $user->getUsername()
        ));
        $insert = new Insert('user');
        $insert->values($postData);
        $sql = new Sql($this->getAdaptor());
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        if ($result instanceof ResultInterface) {
            if ($pk = $result->getGeneratedValue()) {
                $user->setUserId($pk);
            }
            return $hydrator->extract($user);
        }
        throw new \Exception('something went wrong.Please try again later');
    }

    public function update(\Api\Entity\User $user)
    {
        $hydrator = $this->getHydrator();
        $postData = array(
            'display_name' => $user->getDisplayName(),
            'password' => $user->getPassword(),
        );
        $update = new Update('user');
        $update->set($postData);
        $update->where(array(
            'user_id = ?' => $user->getUserId()
        ));
        $sql = new Sql($this->getAdaptor());
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        if ($result instanceof ResultInterface) {
            if ($pk = $result->getGeneratedValue()) {
                $user->setUserId($pk);
            }
            return $this->getUser($user->getUserId());
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
            $this->protoType = new \Api\Entity\User();
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