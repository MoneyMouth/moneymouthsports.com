<?php

namespace Moneymouth\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Moneymouth\AppBundle\Entity\Pool;
use Moneymouth\AppBundle\Entity\Question;
use Moneymouth\AppBundle\Entity\QuestionChoice;
use Moneymouth\AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PoolController extends Controller
{
    /**
     * @Route("/pool/{id}/standings")
     */
    public function standings(Pool $pool)
    {
        return $this->render('pool/standings.html.twig', [
            'pool' => $pool
        ]);
    }

    /**
     * @Route("/pool/{id}/mypicks")
     * @Method("GET")
     */
    public function mypicks(Request $request, Pool $pool)
    {
        /** @var User $user */
        $user = $this->getUser();
        $userQuestionChoices = $user->getChoices();

        $groupedQuestions = [];
        $questions = $pool->getQuestions();
        /** @var Question $question */
        foreach($questions as $question) {
            $groupedQuestions[$question->getQuestionGroup()][] = $question;
        }

        $userChoices = [];
        if(! empty($userQuestionChoices)) {
            /** @var QuestionChoice $choice */
            foreach($userQuestionChoices as $choice) {
                $userChoices[] = $choice->getId();
            }
        }

        return $this->render('pool/mypicks.html.twig', [
            'pool' => $pool,
            'groupedQuestions' => $groupedQuestions,
            'userChoices' => $userChoices
        ]);
    }

    /**
     * @Route("/pool/{id}/mypicks")
     * @Method("POST")
     */
    public function saveMypicks(Request $request, Pool $pool)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $questionChoices = $request->get('question');

        /** @var User $user */
        $user = $this->getUser();

        //First remove all previous choices
        $user->removeAllChoices();

        if(! empty($questionChoices)) {
            foreach($questionChoices as $choiceId) {
                /** @var QuestionChoice $choise */
                $choise = $em->find(QuestionChoice::class, $choiceId);
                //Add new choices
                $user->addChoice($choise);
            }
            $em->persist($user);
            $em->flush();
        }

        return new RedirectResponse($request->getPathInfo());
    }
}
