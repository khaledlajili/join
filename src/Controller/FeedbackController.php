<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\RecruitmentSession;
use App\Form\FeedbackType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('candidate/feedback', name: 'app_feedback')]
    public function index(Request $request): Response {
        $status = [];
        $today = new DateTime();
        $user = $this->getUser();
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $feedback = new Feedback();
        $feedback->setUser($user);
        $access=true;

        //access control
        if ($user->getFeedback() != null) {
            $feedback = $user->getFeedback();
        }

        //preRegistration
        if ($user->getResponses()->isEmpty() || $feedback->getPreRegistrationRating() != null) {
            $status['preRegistration'] = false;
        } else {
            $status['preRegistration'] = true;
        }

        //CollectiveInterview
        if ($user->getCollectiveInterview() != null && $today > $user->getCollectiveInterview()->getEnd() && $feedback->getCollectiveInterviewsRating() == null) {
            $status['collectiveInterview'] = true;
        } else {
            $status['collectiveInterview'] = false;
        }

        //Interview
        if ($user->getInterview() != null && $today > $user->getInterview()->getEnd() && $feedback->getIndividualInterviewsRating() == null) {
            $status['interview'] = true;
        } else {
            $status['interview'] = false;
        }

        //TechnicalTest
        if (!$user->getTechnicalTestResults()->isEmpty() && $feedback->getTechnicalTestRating() == null) {
            $status['technicalTest'] = true;
        } else {
            $status['technicalTest'] = false;
        }

        //TrialPeriod
        if ($user->getResult() != null && $user->getResult()->getTrialPeriod() === true && $today > $recruitmentSession->getTrialPeriodSelectionEnd() && $feedback->getTrialPeriodRating() == null) {
            $status['trialPeriod'] = true;
        } else {
            $status['trialPeriod'] = false;
        }

        //page access control
        if (!in_array(true, $status)) {
            $access=false;
        }

        //Form creation
        $form = $this->createForm(FeedbackType::class, $feedback);

        //Form handle
        $form->handleRequest($request);
        //dd($feedback);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setFeedback($feedback);
            $this->em->persist($feedback);
            $this->em->flush();
            $this->addFlash('success' , 'Your feedback has been successfully submitted.');
            return $this->redirectToRoute('app_candidate');
        }

        return $this->render('feedback/index.html.twig', [
            'status' => $status,
            'feedbackForm' => $form->createView(),
            'access'=>$access
        ]);
    }
}
