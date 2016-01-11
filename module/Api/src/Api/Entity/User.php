<?php
namespace Api\Entity;

use Zend\Db\Sql\Select;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;

class User
{

    protected $user_id;

    protected $username;

    protected $email;

    protected $displayName;

    protected $password;

    protected $state;

    protected $inputFilter;

    private $dbAdapter;

    private $data;


    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }


    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($name)
    {
        $this->displayName = $name;
    }

    public function setData($data)
    {

        $this->data = $data;
    }

    public function setDbAdaptor($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function exchangeArray($data)
    {
        $this->user_id = (!empty($data['user_id'])) ? (int)$data['user_id'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->displayName = (!empty($data['displayName'])) ? $data['displayName'] : null;
        $this->password = (!empty($data['password'])) ? md5($data['password']) : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        //not used
    }


    private function emailUniqueValidator()
    {
        $uniqueValidator = new NoRecordExists(array(
            'table' => 'user',
            'field' => 'email',
            'adapter' => $this->dbAdapter,
            'messages' => array(
                \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'user with this email already exists',
            ),
        ));
        $select = new Select();
        $select->from('user');
        $select->where(array(
            'email= ?' => $this->data->email
        ));
        $uniqueValidator->setSelect($select);
        return $uniqueValidator;
    }

    private function nameUniqueValidator()
    {
        $uniqueValidator = new NoRecordExists(array(
            'table' => 'user',
            'field' => 'username',
            'adapter' => $this->dbAdapter,
            'messages' => array(
                \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'user with this username already exists',
            ),
        ));
        $select = new Select();
        $select->from('user');
        $select->where(array(
            'username= ?' => $this->data->username
        ));
        $uniqueValidator->setSelect($select);
        return $uniqueValidator;
    }

    public function insertInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    $this->nameUniqueValidator(),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'displayName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                    $this->emailUniqueValidator(),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function updateInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(array(
            'name' => 'user_id',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));
        $inputFilter->add(array(
            'name' => 'displayName',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));
        $inputFilter->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));
        return $inputFilter;
    }


}