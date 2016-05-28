<?php

namespace Moneymouth\AppBundle\Repository\User;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * UserRepository.
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->eq('username', $username));
        $criteria->setMaxResults(1);
        $user = $this->matching($criteria)->first();

        return empty($user) ? null : $user;
    }
}
