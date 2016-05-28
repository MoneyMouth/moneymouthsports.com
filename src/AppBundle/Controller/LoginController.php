<?php

namespace Moneymouth\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($this->getUser()) {
            return $this->redirect('/');
        }

        return $this->render('login/login.html.twig', [
            'error' => $error
        ]);
    }

}
