<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 12/1/16
 * Time: 10:35 AM
 */

namespace Api\Form;


use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = 'comment-from', $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'postId',
            'type' => 'Hidden',

        ));
        $this->add(array(
            'name' => 'authorId',
            'type' => 'Hidden',

        ));
    }
}