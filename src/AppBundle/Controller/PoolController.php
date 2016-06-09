<?php

namespace Moneymouth\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Moneymouth\AppBundle\Entity\Pool;
use Moneymouth\AppBundle\Entity\Question;
use Moneymouth\AppBundle\Entity\QuestionChoice;
use Moneymouth\AppBundle\Entity\QuestionType;
use Moneymouth\AppBundle\Entity\User;
use Moneymouth\AppBundle\Repository\Pool\PoolRepository;
use Moneymouth\AppBundle\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
            'standings' => $standings,
        ]);
    }

    /**
     * @Route("/pool/{id}/mypicks")
     * @Method("GET")
     */
    public function mypicks(Request $request, Pool $pool)
    {
        /* @var EntityManager $em */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $userQuestionChoices = $user->getChoices();

        if (!$this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "'.$pool->getName().'" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

        $groupedQuestions = [];
        $questions = $pool->getQuestions();
        /** @var Question $question */
        foreach ($questions as $question) {
            $groupedQuestions[$question->getQuestionGroup()][] = $question;
        }

        $userChoices = [];
        if (!empty($userQuestionChoices)) {
            /** @var QuestionChoice $choice */
            foreach ($userQuestionChoices as $choice) {
                $userChoices[] = $choice->getId();
            }
        }

        return $this->render('pool/mypicks.html.twig', [
            'pool' => $pool,
            'groupedQuestions' => $groupedQuestions,
            'userChoices' => $userChoices,
            'userTieBreakerValue' => $userRepository->getTieBreakerValue($user),
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

        if (!$this->isUserJoinedThePool($pool)) {
            return new RedirectResponse('/');
        }

        //First remove all previous choices
        $user->removeAllChoices();

        if (!empty($questionChoices)) {
            foreach ($questionChoices as $questionId => $choiceId) {
                /** @var Question $question */
                $question = $em->find(Question::class, $questionId);
                $questionType = $question->getType()->getName();

                if ($questionType == QuestionType::QUESTION_RADIO) {

                    /** @var QuestionChoice $choice */
                    $choice = $em->find(QuestionChoice::class, $choiceId);
                    //Add new choices
                    $user->addChoice($choice);
                } elseif ($questionType == QuestionType::QUESTION_TEXT) {

                    /* @var EntityManager $em */
                    $entityManager = $this->getDoctrine()->getManager();

                    /** @var UserRepository $userRepository */
                    $userRepository = $entityManager->getRepository(User::class);

                    $userRepository->updateTieBreakerValue($user, $question, $choiceId);
                }
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->getSaveMypicksFlashMessage($request, $pool)
            );
        }

        return new RedirectResponse($request->getPathInfo());
    }

    /**
     * @param Request $request
     * @param Pool    $pool
     *
     * @return string
     */
    private function getSaveMypicksFlashMessage(Request $request, Pool $pool)
    {
        $questionsCount = count($pool->getQuestions());

        $submittedQuestionsCount = count($request->get('question'));

        if ($questionsCount === $submittedQuestionsCount) {
            $message = 'Your picks are complete!';
        } elseif ($questionsCount > $submittedQuestionsCount) {
            $message = 'Your picks are incomplete!';
        } else {
            throw new RuntimeException('The question count is incorrect');
        }

        return $message;
    }

    /**
     * @Route("/pool/{id}/members")
     */
    public function members(Pool $pool)
    {
        if (!$this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "'.$pool->getName().'" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

        $users = $pool->getUsers();

        return $this->render('pool/members.html.twig', [
            'pool' => $pool,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/pool/{id}/grouppicks")
     */
    public function grouppicks(Pool $pool)
    {
        if (!$this->isUserJoinedThePool($pool)) {
            $this->addFlash(
                'error',
                'To access the pool "'.$pool->getName().'" you have to join it first.'
            );

            return new RedirectResponse('/');
        }

        /** @var PoolRepository $repository */
        $repository = $this->get(PoolRepository::class);
        $groupPicks = $repository->getGroupPicksReport($pool);

        $userPicks = [];
        foreach ($groupPicks as $groupPick) {
            $userPicks[$groupPick['question']]['choices'][$groupPick['label']] = $groupPick['user_count'];
        }

        // Populate the users sum to calculate the percentage
        foreach ($userPicks as $question => $userPick) {
            $userPicks[$question]['sum'] = array_sum($userPick['choices']);
        }

        return $this->render('pool/grouppicks.html.twig', [
            'pool' => $pool,
            'questions' => $pool->getQuestions(),
            'userPicks' => $userPicks,
        ]);
    }

    public function isUserJoinedThePool(Pool $pool)
    {
        $user = $this->getUser();
        $pools = $user->getPools();

        return $pools->contains($pool);
    }
}
