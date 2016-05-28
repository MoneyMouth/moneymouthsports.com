<?php

namespace Moneymouth\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MyAccountController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('myaccount/index.html.twig', array(
            // ...
        ));
    }

}
