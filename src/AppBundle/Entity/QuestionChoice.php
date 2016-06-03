<?php

namespace Moneymouth\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionChoice
 *
 * @ORM\Table(name="question_choice", indexes={@ORM\Index(name="question_id", columns={"question_id"})})
 * @ORM\Entity
 */
class QuestionChoice
{
    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="questionChoices")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

