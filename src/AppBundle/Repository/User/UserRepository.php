<?php

namespace Moneymouth\AppBundle\Repository\User;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Moneymouth\AppBundle\Entity\Question;
use Moneymouth\AppBundle\Entity\QuestionType;
use Moneymouth\AppBundle\Entity\User;
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

    public function getTieBreakerValue(User $user)
    {
        $connection = $this->getEntityManager()->getConnection();

        $report = $connection->fetchColumn('
            SELECT m.`value` FROM `mypicks` m
            INNER JOIN `question` q ON q.`id` = m.`question_id` AND q.`type_id` = 2
            WHERE m.`user_id` = ?
        ', [$user->getId()]);

        return $report;
    }

    public function updateTieBreakerValue(User $user, Question $question, $value)
    {
        if($question->getType()->getName() !== QuestionType::QUESTION_TEXT) {
            throw new InvalidArgumentException;
        }

        $connection = $this->getEntityManager()->getConnection();

        $connection->executeQuery(
            'INSERT INTO `mypicks` SET `user_id` = ?, `question_id` = ?, `value` = ?
             ON DUPLICATE KEY UPDATE value = ?',[$user->getId(), $question->getId(), $value, $value]
        );
    }
}
