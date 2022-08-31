<?php

namespace App\Controller;

use App\Entity\CollectiveInterview;
use App\Entity\CollectiveInterviewsCriterionResult;
use App\Entity\CollectiveInterviewsEvaluationCriterion;
use App\Entity\CollectiveInterviewsResult;
use App\Form\CollectiveInterviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\User;
use App\Form\CollectiveInterviewEvaluationCriterionType;
use App\Form\CollectiveInterviewEvaluationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CollectiveInterviewController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('admin/collective', name: 'app_collective_interview')]
    public function index(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if ($recruitmentSession) {
            if (!$recruitmentSession->getCollectiveInterview()){
                $this->addFlash('error',"There's no collective interview in this session");
                return $this->redirectToRoute('app_admin');
            }
            $collectiveInterview = new CollectiveInterview();
            $form = $this->createForm(CollectiveInterviewType::class, $collectiveInterview);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
                $collectiveInterview->setRecruitmentSession($currentRecruitmentSession);

                foreach ($form['users']->getData() as $user) {
                    $user->setCollectiveInterview($collectiveInterview);
                    $this->em->persist($user);
                }

                $this->em->persist($collectiveInterview);
                $this->em->flush();
                $collectiveInterview = new CollectiveInterview();
                $form = $this->createForm(
                    CollectiveInterviewType::class,
                    $collectiveInterview
                );
                if ($request->isXmlHttpRequest()) {
                    return $this->render(
                        'collective_interview/_collective_interview_form.html.twig',
                        [
                            'collectiveInterviews' => $this->em->getRepository(CollectiveInterview::class)->findAll(),
                            'form' => $form->createView(),
                        ]
                    );
                }
            }
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView(
                    'collective_interview/_collective_interview_form.html.twig',
                    [
                        'collectiveInterviews' => $this->em->getRepository(CollectiveInterview::class)->findAll(),
                        'form' => $form->createView(),
                    ]
                );
                return new Response($html, 400);
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('collective_interview/index.html.twig', [
            'collectiveInterviews' => $this->em->getRepository(CollectiveInterview::class)->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/remove/collective', name: 'remove_collective_interview', methods: ['post'])]
    public function remove(Request $request, MailerInterface $mailer): Response
    {
        $collectiveInterviewId = $request->request->get('id');
        $collectiveInterview = $this->em->getRepository(CollectiveInterview::class)->find(
            $collectiveInterviewId
        );

        if ($collectiveInterview->isEmailSent()) {
            foreach ($collectiveInterview->getRecruiters() as $recruiter) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Cancellation : collective interview ]')
                    ->htmlTemplate('email/collective_interview_cancellation.html.twig')
                    ->context([
                        'user' => $recruiter,
                        'collectiveInterview' => $collectiveInterview
                    ]);
                $mailer->send($email);
            }
            foreach ($collectiveInterview->getUsers() as $candidate) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                    ->subject('[ Cancellation : collective interview ]')
                    ->htmlTemplate('email/collective_interview_cancellation.html.twig')
                    ->context([
                        'user' => $candidate,
                        'collectiveInterview' => $collectiveInterview
                    ]);
                $mailer->send($email);
            }
        }

        $this->em->remove($collectiveInterview);
        $this->em->flush();
        $collectiveInterview = new CollectiveInterview();
        $form = $this->createForm(
            CollectiveInterviewType::class,
            $collectiveInterview
        );
        $form->handleRequest($request);
        return $this->render(
            'collective_interview/_collective_interview_form.html.twig',
            [
                'collectiveInterviews' => $this->em->getRepository(CollectiveInterview::class)->findAll(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/send/email/collective', name: 'send_email_collective_interview', methods: ['post'])]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $collectiveInterviewId = $request->request->get('id');
        $collectiveInterview = $this->em->getRepository(CollectiveInterview::class)->find(
            $collectiveInterviewId
        );
        if (!$collectiveInterview->isEmailSent()) {
            foreach ($collectiveInterview->getRecruiters() as $recruiter) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Invitation : collective interview ]')
                    ->htmlTemplate('email/collective_interview_invitation.html.twig')
                    ->context([
                        'user' => $recruiter,
                        'collectiveInterview' => $collectiveInterview
                    ]);
                $mailer->send($email);
            }
            foreach ($collectiveInterview->getUsers() as $candidate) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                    ->subject('[ Invitation : collective interview ]')
                    ->htmlTemplate('email/collective_interview_invitation.html.twig')
                    ->context([
                        'user' => $candidate,
                        'collectiveInterview' => $collectiveInterview
                    ]);
                $mailer->send($email);
            }
            $collectiveInterview->setEmailSent(true);
            $this->em->flush();
        }
        return $this->render(
            'collective_interview/_collective_interviews_script.html.twig',
            [
                'collectiveInterviews' => $this->em->getRepository(CollectiveInterview::class)->findAll(),
            ]
        );
    }

    #[Route('/admin/collective/evaluation/grid/', name: 'app_collective_interview_evaluation_grid')]
    public function createCriterion(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $criterions = [];
        if(!$recruitmentSession){
            $this->addFlash('error','There\'s no current session');
            $this->redirectToRoute('app_admin');
        }
        elseif (!$recruitmentSession->getCollectiveInterview()){
            $this->addFlash('error',"There's no collective interview in this session");
            return $this->redirectToRoute('app_admin');
        }

        $criterions = $this->em->getRepository(CollectiveInterviewsEvaluationCriterion::class)->findBy(['recruitmentSession' => $recruitmentSession]);


        $criterion = new CollectiveInterviewsEvaluationCriterion();
        $criterionForm = $this->createForm(CollectiveInterviewEvaluationCriterionType::class, $criterion);
        $criterionForm->handleRequest($request);
        if ($criterionForm->isSubmitted() && $criterionForm->isValid()) {
            $criterion->setRecruitmentSession($recruitmentSession);
            $this->em->persist($criterion);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                $criterionRow = $this->renderView('collective_interview/_criterion_row.html.twig',
                    [
                        'collectiveInterviewEvaluationCriterion' => $criterion,
                    ]);

                $criterion = new CollectiveInterviewsEvaluationCriterion();
                $criterionForm = $this->createForm(CollectiveInterviewEvaluationCriterionType::class, $criterion);
                $criterionFormView = $this->renderView('collective_interview/_criterion_form.html.twig',
                    [
                        'criterionForm' => $criterionForm->createView(),
                    ]
                );
                return new JsonResponse(
                    [
                        'collectiveInterviewEvaluationCriterionFormView' => $criterionFormView,
                        'collectiveInterviewEvaluationCriterionRow' => $criterionRow,
                    ],
                    200
                );
            }

            return $this->redirectToRoute('app_collective_interview_evaluation_grid', ['recruitmentSession' => $recruitmentSession->getId()]);

        } else {
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('collective_interview/_criterion_form.html.twig',
                    [
                        'criterionForm' => $criterionForm->createView(),
                    ]
                );
                return new Response($html, 400);
            }
        }
        return $this->render('collective_interview/evaluation_criterion.html.twig', [
            'criterions' => $criterions,
            'criterionForm' => $criterionForm->createView(),
            'recruitmentSession' => $recruitmentSession
        ]);
    }

    #[Route('/admin/collective/evaluation/grid/remove/{criterion}', name: 'app_collective_interview_evaluation_grid_criterion_remove')]
    public function removeCriterion(CollectiveInterviewsEvaluationCriterion $criterion): Response
    {
        $this->em->remove($criterion);
        $this->em->flush();

        return $this->redirectToRoute('app_collective_interview_evaluation_grid');
    }

    #[Route('recruiter/collective/evaluate/{candidate}', name: 'app_collective_evaluation_view')]
    public function EvaluateCandidate(Request $request, User $candidate): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        foreach ($candidate->getCollectiveInterviewsCriterionResults() as $result) {
            if ($result->getRecruiter()->getId() == $this->getUser()->getId()) {
                throw new AccessDeniedException();
            }
        }
        $criteria = $recruitmentSession->getCollectiveInterviewsEvaluationCriteria();
        $form = $this->createForm(CollectiveInterviewEvaluationType::class, [
            'criteria' => $criteria,
        ]);
        $form->handleRequest($request);
        $criteriaTable = [];
        foreach ($criteria as $criterion) {
            $criteriaTable[$criterion->getId()] = $criterion;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData() as $id => $value) {
                if (is_int($id)) {
                    $criterionResult = new CollectiveInterviewsCriterionResult();
                    $criterionResult->setRecruiter($this->getUser());
                    $criterionResult->setCandidate($candidate);
                    $criterionResult->setResult($value);
                    $criterionResult->setCriterion($criteriaTable[$id]);
                    $this->em->persist($criterionResult);
                }
            }
            $result = new CollectiveInterviewsResult();
            $result->setRemark($form->getData()['remarks']);
            $result->setResult($form->getData()['decision']);
            $result->setRecruiter($this->getUser());
            $result->setCandidate($candidate);
            $this->em->persist($result);
            $this->em->flush();
            return $this->redirectToRoute(
                'app_collective_candidates',
                ['collectiveInterview' => $candidate->getCollectiveInterview()->getId()]
            );
        }
        return $this->render('collective_interview/eval.html.twig', [
            'collectiveForm' => $form->createView(),
            'collectiveInterview' => $candidate->getCollectiveInterview()
        ]);
    }

    #[Route('admin/collective/result', name: 'app_collective_result')]
    public function result(): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $users = [];
        if ($recruitmentSession) {
            if (!$recruitmentSession->getCollectiveInterview()){
                $this->addFlash('error',"There's no collective interview in this session");
                return $this->redirectToRoute('app_admin');
            }
            $allusers = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
            foreach ($allusers as $user) {
                if ($user->getResult() && $user->getResult()->getPreRegistration()) {
                    $users[] = $user;
                }
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('collective_interview\result.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('admin/collective/result/data/{user}', name: 'app_collective_registration_single_response', methods: ['post'])]
    public function getResultData(User $user): Response
    {
        $responses = [];
        $values = [];
        $temp = $user->getCollectiveInterviewsCriterionResults();
        foreach ($temp as $response) {
            if (
                !in_array(
                    $response->getRecruiter()->getId(),
                    array_keys($responses)
                )
            ) {
                $responses[$response->getRecruiter()->getId()] = [];
            }
            $responses[$response->getRecruiter()->getId()][] = [
                'criterion' => $response->getCriterion()->getCriterion(),
                'result' => $response->getResult(),
                'recruiter' =>
                    $response->getRecruiter()->getLName() .
                    ' ' .
                    $response->getRecruiter()->getFName(),
            ];
            $values[] = $response->getResult();
        }
        foreach (
            $user->getCollectiveInterviewsResultsAsCandidate()
            as $result
        ) {
            $responses[$result->getRecruiter()->getId()]['decision'] = [
                'remark' => $result->getRemark(),
                'decision' => $result->getResult(),
            ];
        }
        if ($values) {
            $moy = array_sum($values) / count($values);
            $status = 'haveValues';
        } else {
            $moy = 0;
            $status = 'dontHaveValues';
        }
        $template = $this->render(
            'collective_interview/result_data.html.twig',
            ['responses' => $responses, 'user' => $user, 'average' => $moy, 'status' => $status]
        );
        return new JsonResponse([
            'template' => $template->getContent()
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/collective/result/submit', name: 'app_collective_registration_user_result', methods: ['post'])]
    public function postUserResult(Request $request, MailerInterface $mailer): Response
    {
        $data = $request->request->get("result");
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get('user')]);
        if ($data == 'accept') {
            if ($user->getResult() != null) {
                $user->getResult()->setCollectiveInterviews(true);
            } else {
                $result = new Result();
                $result->setCollectiveInterviews(true);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setCollectiveInterviewsEmail(false);
            $this->em->flush();
        } elseif ($data == 'refuse') {
            if ($user->getResult() != null) {
                $user->getResult()->setCollectiveInterviews(false);
            } else {
                $result = new Result();
                $result->setCollectiveInterviews(false);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setCollectiveInterviewsEmail(false);
            $this->em->flush();
        } elseif ($data == "mail") {
            if ($user->getResult() != null) {
                if ($user->getResult()->getCollectiveInterviews() === true) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Collective Interviews Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_accepted.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setCollectiveInterviewsEmail(true);
                } elseif ($user->getResult()->getCollectiveInterviews() === false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Collective Interviews Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_refused.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setCollectiveInterviewsEmail(true);
                }
                $this->em->flush();
            } else {
                $data = 'mail_not_sent';
            }
        }
        return new JsonResponse(['result' => $data]);
    }

    #[Route('recruiter/collective/interviews', name: 'app_collective_interviews')]
    public function getInterviews(): Response
    {
        $interviews = $this->getUser()->getCollectiveInterviews();
        return $this->render('collective_interview\recruiter_interviews.html.twig', [
            'interviews' => $interviews,
        ]);
    }

    #[Route('recruiter/collective/candidates/{collectiveInterview}', name: 'app_collective_candidates')]
    public function getCandidates(CollectiveInterview $collectiveInterview,): Response
    {
        $allusers = $collectiveInterview->getUsers();
        $users = [];
        foreach ($allusers as $user) {
            $allowed = true;
            foreach ($user->getCollectiveInterviewsCriterionResults() as $result) {
                if ($result->getRecruiter()->getId() == $this->getUser()->getId()) {
                    $allowed = false;
                }
            }
            if ($allowed == true)
                $users[] = $user;
        }
        return $this->render('collective_interview\candidates.html.twig', [
            'users' => $users,
        ]);
    }
}
