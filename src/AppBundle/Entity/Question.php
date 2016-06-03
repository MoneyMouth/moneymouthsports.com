<?php

namespace Moneymouth\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question", indexes={@ORM\Index(name="pool_id", columns={"pool_id"})})
 * @ORM\Entity
 */
class Question
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=255, nullable=false)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="question_group", type="string", length=255, nullable=false)
     */
    private $questionGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="correct_choice_id", type="integer", nullable=false)
     */
    private $correctChoiceId;

    /**
     * @ORM\ManyToOne(targetEntity="Pool", inversedBy="questions")
     * @ORM\JoinColumn(name="pool_id", referencedColumnName="id")
     */
    private $pool;

    /**
     * @ORM\OneToMany(targetEntity="QuestionChoice",mappedBy="question")
     */
    private $questionChoices;

    public function __construct()
    {
        $this->questionChoices = new ArrayCollection;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->question;
    }

    /**
     * @return int
     */
    public function getCorrectChoiceId()
    {
        return $this->correctChoiceId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestionGroup()
    {
        return $this->questionGroup;
    }

    /**
     * @return mixed
     */
    public function getChoices()
    {
        return $this->questionChoices;
    }
}

