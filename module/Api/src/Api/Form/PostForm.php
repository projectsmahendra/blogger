<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 11/1/16
 * Time: 2:58 PM
 */

namespace Api\Form;


use Zend\Form\Form;

class PostForm extends Form
{
    public function __construct($name = 'post-from', $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'author',
            'type' => 'Hidden',

        ));
        $this->add(array(
            'name' => 'authorId',
            'type' => 'Hidden',

        ));
    }
}