<?php

namespace Moneymouth\AppBundle\Entity;

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
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=255, nullable=false)
     */
    private $question = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="correct_choice_id", type="integer", nullable=false)
     */
    private $correctChoiceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Moneymouth\AppBundle\Entity\Pool
     *
     * @ORM\ManyToOne(targetEntity="Moneymouth\AppBundle\Entity\Pool")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pool_id", referencedColumnName="id")
     * })
     */
    private $pool;


}

