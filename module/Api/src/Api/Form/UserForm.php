<?php

namespace Api\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = 'user-from', $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' => array(
                'label' => 'User Name',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'displayName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Display Name',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Text',
            'options' => array(
                'label' => 'Password',
            ),
        ));

    }

}