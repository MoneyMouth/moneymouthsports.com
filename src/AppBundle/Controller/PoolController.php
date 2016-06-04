<?php

namespace Moneymouth\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Moneymouth\AppBundle\Entity\Pool;
use Moneymouth\AppBundle\Entity\Question;
use Moneymouth\AppBundle\Entity\QuestionChoice;
use Moneymouth\AppBundle\Entity\User;
use Moneymouth\AppBundle\Repository\Pool\PoolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PoolController extends Controller
{
    /**
     * @Route("/pool/{id}/standings")
     */
    public function standings(Pool $pool)
    {
        /** @var PoolRepository $repository */
        $repository = $this->get(PoolRepository::class);

        $standings = $repository->getStandingsReport($pool);

        return $this->render('pool/standings.html.twig', [
            'pool' => $pool,
            'standings' => $standings
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

        if(! $this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "' . $pool->getName() . '" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

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

        if(! $this->isUserJoinedThePool($pool)) {
            return new RedirectResponse('/');
        }

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

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
        }

        return new RedirectResponse($request->getPathInfo());
    }

    /**
     * @Route("/pool/{id}/members")
     */
    public function members(Pool $pool)
    {
        if(! $this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "' . $pool->getName() . '" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

        $users = $pool->getUsers();

        return $this->render('pool/members.html.twig', [
            'pool' => $pool,
            'users' => $users
        ]);
    }

    /**
     * @Route("/pool/{id}/grouppicks")
     */
    public function grouppicks(Pool $pool)
    {
        if(! $this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "' . $pool->getName() . '" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

        /** @var PoolRepository $repository */
        $repository = $this->get(PoolRepository::class);
        $groupPicks = $repository->getGroupPicksReport($pool);

        $userPicks = [];
        foreach($groupPicks as $groupPick) {
            $userPicks[$groupPick['username']][] = $groupPick['label'];
        }

        return $this->render('pool/grouppicks.html.twig', [
            'pool' => $pool,
            'questions' => $pool->getQuestions(),
            'userPicks' => $userPicks
        ]);
    }

    public function isUserJoinedThePool(Pool $pool)
    {
        $user = $this->getUser();
        $pools = $user->getPools();
        return $pools->contains($pool);
    }
}
