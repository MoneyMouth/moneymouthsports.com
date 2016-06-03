<?php

namespace Moneymouth\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pool
 *
 * @ORM\Table(name="pool", indexes={@ORM\Index(name="group_id", columns={"group_id"})})
 * @ORM\Entity
 */
class Pool
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var PoolGroup
     *
     * @ORM\ManyToOne(targetEntity="PoolGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity="Question",mappedBy="pool")
     */
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PoolGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return Question collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}

