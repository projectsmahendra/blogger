<?php
/**
 * Created by PhpStorm.
 * User: mahendra
 * Date: 11/1/16
 * Time: 2:58 PM
 */

namespace Api\Entity;


use Zend\InputFilter\InputFilter;

class Post
{
    private $id;
    private $title;
    private $description;
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

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
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
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->authorId = (!empty($data['authorId'])) ? $data['authorId'] : null;
    }

    public function inputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));
        $inputFilter->add(array(
            'name' => 'description',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
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