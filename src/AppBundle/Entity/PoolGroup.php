<?php

namespace Moneymouth\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoolGroup
 *
 * @ORM\Table(name="pool_group")
 * @ORM\Entity
 */
class PoolGroup
{
    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=255, nullable=false)
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
}

