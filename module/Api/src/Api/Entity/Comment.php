<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 12/1/16
 * Time: 10:25 AM
 */

namespace Api\Entity;


use Zend\InputFilter\InputFilter;

class Comment
{
    private $id;
    private $descripton;
    private $postId;
    private $author;
    private $authorId;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDescription($description)
    {
        $this->descripton = $description;
    }

    public function getDescription()
    {
        return $this->descripton;
    }

    public function setPostId($id)
    {
        $this->postId = $id;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? (int)$data['id'] : null;
        $this->descripton = (!empty($data['description'])) ? $data['description'] : null;
        $this->postId = (!empty($data['postId'])) ? $data['postId'] : null;
        $this->authorId = (!empty($data['authorId'])) ? $data['authorId'] : null;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(array(
            'name' => 'description',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));
        $inputFilter->add(array(
            'name' => 'postId',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));
        $inputFilter->add(array(
            'name' => 'authorId',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));
        return $inputFilter;
    }
}