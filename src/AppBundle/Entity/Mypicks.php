<?php

namespace Moneymouth\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mypicks
 *
 * @ORM\Table(name="mypicks", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="question_id", columns={"question_id"}), @ORM\Index(name="choice_id", columns={"choice_id"})})
 * @ORM\Entity
 */
class Mypicks
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
     * @var \Moneymouth\AppBundle\Entity\QuestionChoice
     *
     * @ORM\ManyToOne(targetEntity="Moneymouth\AppBundle\Entity\QuestionChoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="choice_id", referencedColumnName="id")
     * })
     */
    private $choice;

    /**
     * @var \Moneymouth\AppBundle\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Moneymouth\AppBundle\Entity\Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * @var \Moneymouth\AppBundle\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="Moneymouth\AppBundle\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}

