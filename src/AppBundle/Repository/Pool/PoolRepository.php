<?php

namespace Moneymouth\AppBundle\Repository\Pool;


use Doctrine\ORM\EntityManager;
use Moneymouth\AppBundle\Entity\Pool;

class PoolRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getStandingsReport(Pool $pool)
    {
        $connection = $this->entityManager->getConnection();

        $report = $connection->fetchAll('
            SELECT count(`correct_choice_id`) AS points,u.id, u.name FROM users u
            INNER JOIN `user_pool` up ON up.`user_id` = u.`id`
            LEFT JOIN `mypicks` m ON m.user_id = u.id
            LEFT JOIN `question_choice` qc ON m.`choice_id` = qc.`id`
            LEFT JOIN question q ON q.id = qc.question_id AND qc.`id` = q.`correct_choice_id`
            WHERE up.`pool_id` = ?
            GROUP BY u.id
            ORDER BY points DESC, u.name ASC
        ', [$pool->getId()]);

        return $report;
    }

    public function getGroupPicksReport(Pool $pool)
    {
        $connection = $this->entityManager->getConnection();

        $report = $connection->fetchAll('
            SELECT q.`question`,qc.`label`, count(u.id) as user_count FROM question_choice qc
            INNER JOIN question q ON q.id = qc.question_id
            LEFT JOIN `mypicks` m ON m.`choice_id` = qc.`id`
            LEFT JOIN users u ON m.user_id = u.id
            WHERE q.`pool_id` = ?
            GROUP BY q.`question`,qc.`label`;
        ', [$pool->getId()]);

        return $report;
    }
}