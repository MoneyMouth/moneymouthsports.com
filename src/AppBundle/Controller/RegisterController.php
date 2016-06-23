<?php

namespace Moneymouth\AppBundle\Controller;

use Moneymouth\AppBundle\Entity\User;
use Moneymouth\AppBundle\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\Email;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirect('/');
        }

        $errors = [];

        if ($request->isMethod('POST') && $this->isFormValid($request)) {

            // Encode the password (you could also do this via Doctrine listener)

            /** @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $encoder */
            $encoder = $this->get('security.password_encoder');

            $user = new User(
                $request->get('_username'),
                $request->get('_password'),
                $request->get('name'),
                $request->get('email')
            );

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->authenticateUser($user);

            return $this->redirect('/');
        } elseif($request->isMethod('POST')) {
            $errors = $this->validateForm($request);
        }

        return $this->render(
            'register/register.html.twig', [
                'errors' => $errors
            ]
        );
    }

    private function validateForm(Request $request)
    {
        $errors = [];

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()
            ->getRepository('AppBundle:User');

        $username = $request->get('_username');
        $user = $userRepo->loadUserByUsername($username);
        if(! is_null($user)) {
            $errors[] = 'User name already exists. Please choose a different one.';
        }

        if(empty($username)) {
            $errors[] = 'Please enter your username.';
        }

        if(empty($request->get('_password'))) {
            $errors[] = 'Please enter the password.';
        }

        if(empty($request->get('name'))) {
            $errors[] = 'Please enter your name.';
        }

        if(empty($request->get('email'))) {
            $errors[] = 'Please enter correct email.';
        }

        /** @var \Symfony\Component\Validator\Validator\RecursiveValidator $validator */
        $validator = $this->get('validator');

        /** @var \Symfony\Component\Validator\ConstraintViolationList $error */
        $error = $validator->validate($request->get('email'), new Email());
        if($error->count()) {
            $errors[] = 'Invalid email.';
        }

        if($request->get('_password') !== $request->get('confirm_password')) {
            $errors[] = 'The passwords are not the same.';
        }

        return $errors;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isFormValid(Request $request)
    {
        return empty($this->validateForm($request));
    }

    /**
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
    }

}
