<?php

namespace Moneymouth\AppBundle\Controller;

use Moneymouth\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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
                $request->get('username'),
                $request->get('password'),
                $request->get('name'),
                $request->get('email')
            );

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('login');
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
        if(empty($request->get('username'))) {
            $errors[] = 'Please enter your username.';
        }

        if(empty($request->get('password'))) {
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
        };

        if($request->get('password') !== $request->get('confirm_password')) {
            $errors[] = 'The passwords are not the same.';
        }

        return $errors;
    }

    private function isFormValid(Request $request)
    {
        return empty($this->validateForm($request));
    }

}
