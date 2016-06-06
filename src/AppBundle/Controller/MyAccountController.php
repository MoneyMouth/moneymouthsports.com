<?php

namespace Moneymouth\AppBundle\Controller;

use Moneymouth\AppBundle\Entity\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MyAccountController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $pools = $user->getPools();

        return $this->render('myaccount/index.html.twig', [
            'groupedPools' => $this->groupPools($pools),
        ]);
    }

    private function groupPools($pools)
    {
        $groupedPools = [];

        /** @var Pool $pool */
        foreach ($pools as $pool) {
            $groupName = $pool->getGroup()->getName();
            $groupedPools[$groupName][] = $pool;
        }

        return $groupedPools;
    }
}
