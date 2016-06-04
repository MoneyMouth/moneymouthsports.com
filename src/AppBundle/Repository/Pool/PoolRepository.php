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
            SELECT count(`correct_choice_id`) AS points,m.user_id, u.name FROM `mypicks` m
            INNER JOIN `question_choice` qc ON m.`choice_id` = qc.`id`
            INNER JOIN question q ON q.id = qc.question_id AND qc.`id` = q.`correct_choice_id`
            INNER JOIN users u ON m.user_id = u.id
            WHERE q.`pool_id` = ?
            GROUP BY user_id
            ORDER BY points DESC
        ', [$pool->getId()]);

        return $report;
    }

    public function getGroupPicksReport(Pool $pool)
    {
        $connection = $this->entityManager->getConnection();

        $report = $connection->fetchAll('
            SELECT u.username,q.`question`,qc.`label` FROM `mypicks` m
            INNER JOIN `question_choice` qc ON m.`choice_id` = qc.`id`
            INNER JOIN question q ON q.id = qc.question_id
            INNER JOIN users u ON m.user_id = u.id
            WHERE q.`pool_id` = 1
        ', [$pool->getId()]);

        return $report;
    }
}