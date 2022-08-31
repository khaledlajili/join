<?php

namespace App\Controller;


use App\Entity\Demande;
use App\Entity\Department;
use App\Entity\Interview;
use App\Form\EmergencyInterviewType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\InterviewCriterionResult;
use App\Entity\InterviewsAvailability;
use App\Entity\InterviewsEvaluationSheetPart;
use App\Entity\InterviewsResult;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\User;
use App\Form\CancelDemandeType;
use App\Form\ChangeInterviewType;
use App\Form\InterviewEvaluationSheetPartType;
use App\Form\InterviewEvaluationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\InterviewType;
use App\Form\ValidateInterviewType;
use App\Repository\DemandeRepository;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class IndividualInterviewController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Initial interview creation
    #[Route('admin/interview', name: 'app_individual_interview')]
    public function index(Request $request): Response
    {
        $today = new DateTime();
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        if (!$currentRecruitmentSession || $today > $currentRecruitmentSession->getInterviewsScheduleEnd())
            $access = false;
        else
            $access = true;

        $interview = new Interview();
        $form = $this->createForm(
            InterviewType::class,
            $interview
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $interview->setRecruitmentSession(
                $currentRecruitmentSession
            );

            $this->em->persist($interview);
            $this->em->flush();
            $interview = new Interview();
            $form = $this->createForm(
                InterviewType::class,
                $interview
            );
            if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'individual_interview/_interview_form.html.twig',
                    [
                        'Interviews' => $currentRecruitmentSession->getInterviews(),
                        'form' => $form->createView(),
                    ]
                );
            }
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView(
                'individual_interview/_interview_form.html.twig',
                [
                    'Interviews' => $currentRecruitmentSession->getInterviews(),
                    'form' => $form->createView(),
                ]
            );
            return new Response($html, 400);
        }
        return $this->render('individual_interview/index.html.twig', [
            'Interviews' => $currentRecruitmentSession->getInterviews(),
            'form' => $form->createView(),
            'access' => $access
        ]);
    }


    #[Route('admin/remove/interview', name: 'remove_individual_interview')]
    public function remove(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $InterviewId = $request->request->get('id');
        $Interview = $this->em->getRepository(Interview::class)->find(
            $InterviewId
        );
        $this->em->remove($Interview);
        $this->em->flush();
        $Interview = new Interview();
        $form = $this->createForm(
            InterviewType::class,
            $Interview
        );
        $form->handleRequest($request);
        return $this->render(
            'individual_interview/_interview_form.html.twig',
            [
                'Interviews' => $recruitmentSession->getInterviews(),
                'form' => $form->createView(),
            ]
        );
    }

    //Interview validation collective
    #[Route('admin/interview/create', name: 'app_individual_interview_create')]
    public function createInterview(Request $request): Response
    {
        $today = new DateTime();
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        if (!$recruitmentSession || ($today > $recruitmentSession->getForBookingInterviewsScheduleEnd() && $recruitmentSession->getBookingForInterview()) || (($today > $recruitmentSession->getValidateInterviewsScheduleEnd() && !$recruitmentSession->getBookingForInterview())))
            $access = false;
        else
            $access = true;

        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView(
                'individual_interview/_create_form_script.html.twig',
                [
                    'Interviews' => $recruitmentSession->getInterviews(),
                ]
            );
            return new Response($html, 400);
        }
        return $this->render('individual_interview/create.html.twig', [
            'Interviews' => $recruitmentSession->getInterviews(),
            'access' => $access

        ]);
    }

    #[Route('admin/interview/getData', name: 'data_individual_interview')]
    public function getCreateFrom(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $InterviewId = $request->request->get('id');
        $Interview = $this->em->getRepository(Interview::class)->find(
            $InterviewId
        );
        if ($Interview->isEmailSent()) {
            return $this->render(
                'individual_interview/_interview_data.html.twig',
                [
                    'interview' => $Interview,
                    'Interviews' => $recruitmentSession->getInterviews()
                ]
            );
        }
        $form = $this->createForm(
            ValidateInterviewType::class,
            $Interview
        )->add('id', HiddenType::class, [
            'mapped' => true
        ]);
        $form->handleRequest($request);


        return $this->render(
            'individual_interview/_create_form_popup.html.twig',
            [
                'Interviews' => $recruitmentSession->getInterviews(),
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('admin/interview/submitData', name: 'data_submit_individual_interview')]
    public function submitCreateFrom(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $InterviewId = $request->request->all()['validate_interview']['id'];
        $Interview = $this->em->getRepository(Interview::class)->find(
            $InterviewId
        );
        $form = $this->createForm(
            ValidateInterviewType::class,
            $Interview
        )->add('id', HiddenType::class, [
            'mapped' => false
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($Interview);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'individual_interview/_create_form_popup.html.twig',
                    [
                        'Interviews' => $recruitmentSession->getInterviews(),
                        'form' => $form->createView(),
                    ]
                );
            }
        }

        return $this->render(
            'individual_interview/_create_form_script.html.twig',
            [
                'Interviews' => $recruitmentSession->getInterviews(),
            ]
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/send/email/interview', name: 'send_email_confirmation_interview', methods: ['post'])]
    public function sendConfirmatonEmail(Request $request, MailerInterface $mailer): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $individualInterviewId = $request->request->get('id');
        $individualInterview = $this->em->getRepository(Interview::class)->find(
            $individualInterviewId
        );
        $candidate = $individualInterview->getCandidate();
        if (!$individualInterview->isEmailSent()) {
            foreach ($individualInterview->getRecruiters() as $recruiter) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Invitation : Interview ]')
                    ->htmlTemplate('email/interview_invitation.html.twig')
                    ->context([
                        'user' => $recruiter,
                        'interview' => $individualInterview
                    ]);
                $mailer->send($email);
            }

            $email = (new TemplatedEmail())
                ->from(new Address('email@your-domain.com', 'JOIN'))
                ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                ->subject('[ Invitation : Interview invitation ]')
                ->htmlTemplate('email/interview_invitation.html.twig')
                ->context([
                    'user' => $candidate,
                    'interview' => $individualInterview
                ]);
            $mailer->send($email);
        }
        $individualInterview->setEmailSent(true);
        $this->em->flush();

        return $this->render(
            'individual_interview/_create_form_script.html.twig',
            [
                'Interviews' => $recruitmentSession->getInterviews(),
            ]
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/interview/cancel', name: 'cancel_interview', methods: ['post'])]
    public function cancelInterview(Request $request, MailerInterface $mailer): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );

        if ($interview->isEmailSent()) {
            foreach ($interview->getRecruiters() as $recruiter) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Cancellation : Interview ]')
                    ->htmlTemplate('email/interview_cancellation.html.twig')
                    ->context([
                        'user' => $recruiter,
                        'interview' => $interview
                    ]);
                $mailer->send($email);
            }
            $candidate = $interview->getCandidate();
            $email = (new TemplatedEmail())
                ->from(new Address('email@your-domain.com', 'JOIN'))
                ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                ->subject('[ Cancellation : Interview ]')
                ->htmlTemplate('email/interview_cancellation.html.twig')
                ->context([
                    'user' => $candidate,
                    'interview' => $interview
                ]);
            $mailer->send($email);

        }


        $this->em->remove($interview);
        $this->em->flush();
        return $this->render(
            'individual_interview/_create_form_script.html.twig',
            [
                'Interviews' => $recruitmentSession->getInterviews(),
            ]
        );
    }

    #[Route('admin/interview/emergency/cancel', name: 'cancel_emergency_interview', methods: ['post'])]
    public function cancelEmergencyInterview(Request $request, MailerInterface $mailer): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );

        if ($interview->isEmailSent()) {
            foreach ($interview->getRecruiters() as $recruiter) {
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Cancellation : Interview ]')
                    ->htmlTemplate('email/interview_cancellation.html.twig')
                    ->context([
                        'user' => $recruiter,
                        'interview' => $interview
                    ]);
                $mailer->send($email);
            }

            $candidate = $interview->getCandidate();
            $email = (new TemplatedEmail())
                ->from(new Address('email@your-domain.com', 'JOIN'))
                ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                ->subject('[ Cancellation : Interview ]')
                ->htmlTemplate('email/interview_cancellation.html.twig')
                ->context([
                    'user' => $candidate,
                    'interview' => $interview
                ]);
            $mailer->send($email);

        } elseif ($interview->getRecruitmentSession()->getBookingForInterview() && $interview->getCandidate()) {
            $candidate = $interview->getCandidate();
            $email = (new TemplatedEmail())
                ->from(new Address('email@your-domain.com', 'JOIN'))
                ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                ->subject('[ Cancellation : Interview ]')
                ->htmlTemplate('email/interview_cancellation.html.twig')
                ->context([
                    'user' => $candidate,
                    'interview' => $interview
                ]);
            $mailer->send($email);
        }


        $this->em->remove($interview);
        $this->em->flush();


        $interview = new Interview();
        $form = $this->createForm(
            EmergencyInterviewType::class,
            $interview
        );
        $form->handleRequest($request);
        return $this->render(
            'individual_interview/_emergency_interview_form.html.twig',
            [
                'interviews' => $recruitmentSession->getInterviews(),
                'form' => $form->createView(),
            ]
        );
    }

    //Availibily Calendar
    #[Route('recruiter/interview/availability', name: 'app_individual_interview_availability')]
    public function availibility(Request $request): Response
    {
        $today = new DateTime();
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        if (!$recruitmentSession || $today > $recruitmentSession->getRecruitersAvailabilityEnd())
            $status = "cantAccess";
        else
            $status = "canAccess";

        $availibilities = [];
        foreach ($this->getUser()->getInterviewsAvailabilities() as $availibilitie) {
            $availibilities[$availibilitie->getInterview()->getId()] = $availibilitie->getAvailable();
        }

        return $this->render('individual_interview/availibility.html.twig', [
            'Interviews' => $recruitmentSession->getInterviews(),
            'user' => $this->getUser(),
            'availibilities' => $availibilities,
            'status' => $status

        ]);
    }

    #[Route('recruiter/interview/available', name: 'app_individual_interview_availability_submit')]
    public function availibilitySubmit(Request $request): Response
    {
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );
        $interviewAvailibility = $this->em->getRepository(InterviewsAvailability::class)->findOneBy(
            ['interview' => $interview, 'recruiter' => $this->getUser()]
        );
        if ($interviewAvailibility) {
            $interviewAvailibility->setAvailable(true);
            $this->em->persist($interviewAvailibility);
        } else {
            $interviewAvailibility = new InterviewsAvailability();
            $interviewAvailibility->setInterview($interview);
            $interviewAvailibility->setRecruiter($this->getUser());
            $interviewAvailibility->setAvailable(true);
            $this->em->persist($interviewAvailibility);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_individual_interview_availability');


    }

    #[Route('recruiter/interview/notAvailable', name: 'app_individual_interview_not_availability_submit')]
    public function NoneAvailibilitySubmit(Request $request): Response
    {
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );
        $interviewAvailibility = $this->em->getRepository(InterviewsAvailability::class)->findOneBy(
            ['interview' => $interview, 'recruiter' => $this->getUser()]
        );
        if ($interviewAvailibility) {
            $interviewAvailibility->setAvailable(false);
            $this->em->persist($interviewAvailibility);
        } else {
            $interviewAvailibility = new InterviewsAvailability();
            $interviewAvailibility->setInterview($interview);
            $interviewAvailibility->setRecruiter($this->getUser());
            $interviewAvailibility->setAvailable(false);
            $this->em->persist($interviewAvailibility);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_individual_interview_availability');


    }

    //emergencyCalendar

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/interview/emergency', name: 'app_interview_emergency')]
    public function emergencyCalendar(Request $request, MailerInterface $mailer): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        $interview = new Interview();
        $form = $this->createForm(EmergencyInterviewType::class, $interview);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $interview->setRecruitmentSession($currentRecruitmentSession);


            $this->em->persist($interview);
            $this->em->flush();
            $candidate = $interview->getCandidate();
            if (!$interview->isEmailSent()) {
                foreach ($interview->getRecruiters() as $recruiter) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                        ->subject('[ Invitation : collective interview ]')
                        ->htmlTemplate('email/interview_invitation.html.twig')
                        ->context([
                            'user' => $recruiter,
                            'interview' => $interview
                        ]);
                    $mailer->send($email);
                }

                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($candidate->getEmail(), $candidate->getFName() . ' ' . $candidate->getLName()))
                    ->subject('[ Invitation : Interview invitation ]')
                    ->htmlTemplate('email/interview_invitation.html.twig')
                    ->context([
                        'user' => $candidate,
                        'interview' => $interview
                    ]);
                $mailer->send($email);
            }
            $interview->setEmailSent(true);
            $this->em->flush();

            $interview = new Interview();
            $form = $this->createForm(
                EmergencyInterviewType::class,
                $interview
            );
            if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'individual_interview/_emergency_interview_form.html.twig',
                    [
                        'interviews' => $currentRecruitmentSession->getInterviews(),
                        'form' => $form->createView(),
                    ]
                );
            }
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView(
                'individual_interview/_emergency_interview_form.html.twig',
                [
                    'interviews' => $currentRecruitmentSession->getInterviews(),
                    'form' => $form->createView(),
                ]
            );
            return new Response($html, 400);
        }
        return $this->render('individual_interview/emergency.html.twig', [
            'interviews' => $currentRecruitmentSession->getInterviews(),
            'form' => $form->createView(),
        ]);
    }


    //Booking Calendar
    #[Route('candidate/interview/booking', name: 'app_individual_interview_booking')]
    public function booking(Request $request): Response
    {
        $today = new DateTime();
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        if (!$recruitmentSession || !$recruitmentSession->getBookingForInterview() || $today > $recruitmentSession->getValidateInterviewsScheduleEnd() || $today < $recruitmentSession->getForBookingInterviewsScheduleEnd() || (!$this->getUser()->getDemandes()->isEmpty()))
            $access = false;
        else
            $access = true;

        $state = [];
        $interviews = [];
        foreach ($recruitmentSession->getInterviews() as $interview) {
            if (!empty($interview->getRecruiters()->toArray())) {
                $interviews[] = $interview;
                if ($this->getUser()->getInterview() == $interview) {
                    $state[$interview->getId()] = "currentBooked";
                } else if ($interview->getCandidate()) {
                    $state[$interview->getId()] = "notAvailible";
                } else {
                    if ($this->getUser()->getInterview()) {
                        $state[$interview->getId()] = "alreadyBooked";
                    } else
                        $state[$interview->getId()] = "availibile";
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView(
                'individual_interview/_booking_data.html.twig',
                [
                    'Interviews' => $interviews,
                    'user' => $this->getUser(),
                    'status' => $state

                ]
            );
            return new Response($html, 400);
        }
        return $this->render('individual_interview/booking.html.twig', [
            'Interviews' => $interviews,
            'user' => $this->getUser(),
            'status' => $state,
            'access' => $access

        ]);
    }


    #[Route('candidate/interview/book', name: 'app_individual_interview_book_submit')]
    public function bookInterview(Request $request): Response
    {
        $today = new DateTime();
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );
        $bookedInterview = $this->getUser()->getInterview();
        if ($bookedInterview) {
            $limitinf = date_add(new DateTime(), date_interval_create_from_date_string("1 days"));
            $limintinfAlready = date_add(new DateTime(), date_interval_create_from_date_string("2 days"));
            if ($bookedInterview->getStart() > $limintinfAlready && $interview->getStart() > $limitinf && !$interview->getCandidate()) {
                $this->getUser()->setInterview(null);
                $interview->setCandidate($this->getUser());
                $this->addFlash("success", "You have successfully made your booking");
                $this->em->flush();
                return $this->redirectToRoute('app_candidate');
            } else {
                if ($interview->getCandidate()) {
                    $this->addFlash("error", "This date is unavailible");
                    return $this->redirectToRoute('app_individual_interview_booking');
                }
                if ($bookedInterview->getStart() < $limintinfAlready) {
                    $this->addFlash("error", "You cant change your booking before 2 days from its date");
                    return $this->redirectToRoute('app_individual_interview_booking');
                }
                if ($interview->getStart() < $limitinf) {
                    $this->addFlash("error", "You have to book and interview after at least 24h");
                    return $this->redirectToRoute('app_individual_interview_booking');
                }
            }

        } else {
            $limitinf = date_add(new DateTime(), date_interval_create_from_date_string("1 days"));
            if ($interview->getStart() > $limitinf && !$interview->getCandidate()) {
                $interview->setCandidate($this->getUser());
                $this->em->flush();
                $this->addFlash("success", "You have successfully made your booking");
                return $this->redirectToRoute('app_candidate');
            } else {
                if ($interview->getCandidate()) {
                    $this->addFlash("error", "This date is unavailible");
                    return $this->redirectToRoute('app_individual_interview_booking');
                } else {
                    $this->addFlash("error", "You have to book and interview after at least 24h");
                    return $this->redirectToRoute('app_individual_interview_booking');
                }

            }
        }
        return $this->redirectToRoute('app_individual_interview_booking');


    }

    //Cancel booking & demande submit
    #[Route('candidate/interview/cancel', name: 'app_individual_interview_book_cancel')]
    public function cancelBooking(Request $request): Response
    {
        $today = new DateTime();
        $interviewId = $request->request->get('id');
        $interview = $this->em->getRepository(Interview::class)->find(
            $interviewId
        );
        $demande = new Demande();
        $demande->setInterview($interview);
        $demande->setSender($this->getUser());
        $this->getUser()->setInterview(null);
        $this->em->persist($demande);
        $this->em->flush();
        $form = $this->createForm(
            CancelDemandeType::class,
            $demande
        )->add('id', HiddenType::class, [
            'mapped' => true
        ]);
        $form->handleRequest($request);

        return $this->render(
            'individual_interview/demande.html.twig',
            [
                'form' => $form->createView(),
            ]
        );


    }

    #[Route('candidate/interview/submitDemande', name: 'app_individual_interview_submit_demande')]
    public function submitDemande(Request $request, DemandeRepository $demandeRepository): Response
    {
        $demandeId = $request->request->all()['cancel_demande']['id'];
        $demande = $demandeRepository->find(
            $demandeId
        );
        $form = $this->createForm(
            CancelDemandeType::class,
            $demande
        )->add('id', HiddenType::class, [
            'mapped' => false
        ]);;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($demande);
            $this->em->flush();
            $this->addFlash(
                'success',
                'Your demande is succesfully sent to the admin'
            );
            return $this->redirectToRoute('app_candidate');
        }

        $this->addFlash(
            'error',
            "Unknown error"
        );
        return $this->redirectToRoute('app_candidate');
    }

    #[Route('admin/interview/demandes', name: 'app_individual_demandes')]
    public function demandesView(): Response
    {
        $demandes = [];
        $allDemandes = $this->em->getRepository(Demande::class)->findAll();
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(["current" => true]);
        foreach ($allDemandes as $demande) {
            if ($demande->getSender()->getRecruitmentSession()->getId() == $recruitmentSession->getId())
                $demandes[] = $demande;
        }
        return $this->render('individual_interview\demandesList.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('admin/interview/change/{id}', name: 'app_individual_change')]
    public function interviewChange(User $user, Request $request): Response
    {
        $form = $this->createForm(ChangeInterviewType::class, $user)->add('id', HiddenType::class, [
            'mapped' => true
        ]);
        $form->handleRequest($request);
        return $this->render('individual_interview\_change_interview.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/interview/submitChange', name: 'app_individual_change_submit')]
    public function interviewChangeSubmit(Request $request, MailerInterface $mailer): Response
    {
        $userid = $request->request->all()['change_interview']['id'];
        $user = $this->em->getRepository(User::class)->find(
            $userid
        );
        $form = $this->createForm(ChangeInterviewType::class, $user)->add('id', HiddenType::class, [
            'mapped' => false
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('email@your-domain.com', 'JOIN'))
                ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                ->subject('[ Invitation : Interview invitation ]')
                ->htmlTemplate('email/interview_invitation.html.twig')
                ->context([
                    'user' => $user,
                    'interview' => $user->getInterview()
                ]);
            $mailer->send($email);

            if ($request->isXmlHttpRequest()) {
                return $this->render(
                    'individual_interview\_change_interview.html.twig',
                    [
                        'form' => $form->createView()
                    ]
                );
            }
        }
        return $this->render('individual_interview\_change_interview.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/admin/interview/evaluation/grid', name: 'app_interview_evaluation_grid')]
    public function createSheet(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $sheetParts = [];
        if ($recruitmentSession) {
            $sheetParts = $this->em->getRepository(InterviewsEvaluationSheetPart::class)->findBy(['recruitmentSession' => $recruitmentSession]);
        }

        $sheetPart = new InterviewsEvaluationSheetPart();
        $SheetPartForm = $this->createForm(InterviewEvaluationSheetPartType::class, $sheetPart);
        $SheetPartForm->handleRequest($request);
        if ($SheetPartForm->isSubmitted() && $SheetPartForm->isValid()) {
            if (count($SheetPartForm['interviewsEvaluationCriteria']->getData()) < 1) {
                $this->addFlash('error', 'you should enter at least a criterion.');
                $html = $this->renderView('individual_interview/_sheetpart_form.html.twig', [
                    'SheetPartForm' => $SheetPartForm->createView()
                ]);
                return new Response($html, 400);
            } else {
                $sheetPart->setRecruitmentSession($recruitmentSession);
                $this->em->persist($sheetPart);
                $this->em->flush($sheetPart);
                if ($request->isXmlHttpRequest()) {
                    $sheetPartItem = $this->renderView('individual_interview/_sheetpart_item.html.twig', [
                        'sheetPart' => $sheetPart
                    ]);

                    $sheetPart = new InterviewsEvaluationSheetPart();
                    $SheetPartForm = $this->createForm(InterviewEvaluationSheetPartType::class, $sheetPart);
                    $SheetPartFormView = $this->renderView('individual_interview/_sheetpart_form.html.twig', [
                        'recruitmentSession' => $recruitmentSession,
                        'SheetPartForm' => $SheetPartForm->createView()
                    ]);
                    return new JsonResponse(['SheetPartFormView' => $SheetPartFormView, 'sheetPartItem' => $sheetPartItem], 200);
                }

                return $this->redirectToRoute('app_interview_evaluation_grid', ['recruitmentSession' => $recruitmentSession->getId()]);
            }
        } else {
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('individual_interview/_sheetpart_form.html.twig', [
                    'recruitmentSession' => $recruitmentSession,
                    'SheetPartForm' => $SheetPartForm->createView()
                ]);
                return new Response($html, 400);
            }
        }

        return $this->render('individual_interview/sheet_parts.html.twig', [
            'sheetParts' => $sheetParts,
            'recruitmentSession' => $recruitmentSession,
            'SheetPartForm' => $SheetPartForm->createView()
        ]);
    }

    #[Route('/admin/interview/evaluation/grid/sheet/get', name: 'app_interview_evaluation_grid_get_sheet', methods: ['post'])]
    public function getSheetPartData(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $sheetPart = $this->em->getRepository(InterviewsEvaluationSheetPart::class)->find($request->request->get('sheetPartId'));
        $SheetPartForm = $this->createForm(InterviewEvaluationSheetPartType::class, $sheetPart)
            ->add('id', HiddenType::class, [
                'mapped' => true
            ]);
        $SheetPartForm->handleRequest($request);
        return $this->render('individual_interview/_edit_sheet_part_form.html.twig', [
            'recruitmentSession' => $recruitmentSession,
            'SheetPartForm' => $SheetPartForm->createView(),
            'sheetPartId' => $sheetPart->getId()
        ]);

    }

    #[Route('/admin/interview/evaluation/grid/sheet/edit', name: 'app_interview_evaluation_grid_edit_sheet', methods: ['post'])]
    public function editSheetPart(Request $request): Response
    {
        $sheetPartId = $request->request->all()['interview_evaluation_sheet_part']['id'];
        $sheetPart = $this->em->getRepository(InterviewsEvaluationSheetPart::class)->find($sheetPartId);
        $originalCriteria = new ArrayCollection();
        foreach ($sheetPart->getInterviewsEvaluationCriteria() as $criterion) {
            $originalCriteria->add($criterion);
        }

        $SheetPartForm = $this->createForm(InterviewEvaluationSheetPartType::class, $sheetPart)
            ->add('id', HiddenType::class, [
                'mapped' => false
            ]);
        $SheetPartForm->handleRequest($request);

        if ($SheetPartForm->isSubmitted() && $SheetPartForm->isValid()) {
            if (count($SheetPartForm['interviewsEvaluationCriteria']->getData()) < 1) {
                $this->addFlash('error', 'you should enter at least a criterion.');
                $html = $this->renderView('individual_interview/_edit_sheet_part_form.html.twig', [
                    'SheetPartForm' => $SheetPartForm->createView(),
                    'sheetPartId' => $sheetPartId
                ]);
                return new Response($html, 400);
            }
            foreach ($originalCriteria as $criterion) {
                $this->em->remove($criterion);
            }
            $this->em->persist($sheetPart);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                $sheetPartItem = $this->renderView('individual_interview/_sheetpart_item.html.twig', [
                    'sheetPart' => $sheetPart
                ]);

                return new Response($sheetPartItem, 200);
            }
            return $this->redirectToRoute('app_interview_evaluation_grid');
        }
        return $this->redirectToRoute('app_interview_evaluation_grid');
    }

    #[Route('/admin/interview/evaluation/grid/sheet/remove/{sheetPart}', name: 'app_interview_evaluation_grid_remove_sheet')]
    public function removeSheetPart(InterviewsEvaluationSheetPart $sheetPart): Response
    {
        $this->em->remove($sheetPart);
        $this->em->flush();
        return $this->redirectToRoute('app_interview_evaluation_grid');
    }


    //Evaluation & Results
    #[Route('recruiter/interview/evaluation/view/{candidate}', name: 'app_individual_evaluation_view')]
    public function EvaluateCandidate(Request $request, User $candidate): Response
    {
        $recruitmentSession = $candidate->getRecruitmentSession();
        foreach ($candidate->getInterviewCriterionResults() as $result) {
            if ($result->getRecruiter()->getId() == $this->getUser()->getId()) {
                throw new AccessDeniedException();
            }
        }
        $criteria = [];
        foreach ($candidate->getDepartments() as $department) {
            foreach (
                $department
                    ->getInterviewsEvaluationSheetParts()
                as $sheetPart
            ) {
                $criteria[$sheetPart->getName()] = $sheetPart->getInterviewsEvaluationCriteria()->toArray();
            }
        }
        $form = $this->createForm(InterviewEvaluationType::class, [
            'criteria' => $criteria,
        ]);
        $sheets = $recruitmentSession->getInterviewsEvaluationSheetParts();
        $sheetsTable = [];
        foreach ($sheets as $sheet) {
            $sheetsTable[$sheet->getName()] = $sheet;
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()['criteria'] as $sheetKey => $sheet) {
                foreach ($sheet as $rank => $criterion) {
                    $criterionResult = new InterviewCriterionResult();
                    $criterionResult->setResult(
                        $form->getData()[$sheetKey][$criterion->getId()]
                    );
                    $criterionResult->setCriterion($criterion);
                    $criterionResult->setCandidate($candidate);
                    $criterionResult->setRecruiter($this->getUser());
                    $this->em->persist($criterionResult);
                }
            }
            $result = new InterviewsResult();
            $result->setRemark($form->getData()['remarks']);
            $result->setResult($form->getData()['decision']);
            $result->setRecruiters($this->getUser());
            $result->setCandidate($candidate);
            $this->em->persist($result);
            $this->em->flush();
            return $this->redirectToRoute(
                'app_individual_interviews_list',
                ['id' => $recruitmentSession->getId()]
            );
        }
        return $this->render('individual_interview/eval.html.twig', [
            'individualForm' => $form->createView(),
            'interview' => $candidate->getInterview()
        ]);
    }

    #[Route('admin/interview/result', name: 'app_individual_result')]
    public function result(): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $users = [];
        if ($recruitmentSession) {
            $allusers = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
            $departments = $this->em->getRepository(Department::class)->findAll();
            foreach ($allusers as $user) {
                if ($user->getResult() && ((!$recruitmentSession->getTechnicalTest() && !$recruitmentSession->getCollectiveInterview() && $user->getResult()->getPreRegistration()) || (!$recruitmentSession->getTechnicalTest() && $user->getResult()->getCollectiveInterviews()) || $user->getResult()->getTechnicalTestResult())) {
                    $users[] = $user;
                }
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('individual_interview\result.html.twig', [
            'users' => $users,
            'departments' => $departments,
        ]);
    }

    #[Route('admin/interview/result/data/{user}', name: 'app_individual_registration_single_response')]
    public function getResultData(User $user): Response
    {
        $responses = [];
        $values = [];
        $temp = $user->getInterviewCriterionResults();
        foreach ($temp as $response) {
            if (
                in_array($response->getRecruiter()->getId(), array_keys($responses))) {
                if (!in_array($response->getCriterion()->getSheetPart()->getName(), array_keys($responses[$response->getRecruiter()->getId()]))) {
                    $responses[$response->getRecruiter()->getId()][$response
                        ->getCriterion()
                        ->getSheetPart()
                        ->getName()] = [];
                }
            } else {
                $responses[$response->getRecruiter()->getId()] = [];
                $responses[$response->getRecruiter()->getId()][$response
                    ->getCriterion()
                    ->getSheetPart()
                    ->getName()] = [];
            }
            $responses[$response->getRecruiter()->getId()][$response->getCriterion()->getSheetPart()->getName()][] = [
                'criterion' => $response
                    ->getCriterion()
                    ->getCriterion(),
                'result' => $response->getResult(),
                'recruiter' =>
                    $response->getRecruiter()->getLName() .
                    ' ' .
                    $response->getRecruiter()->getFName(),
            ];
            if (in_array($response->getCriterion()->getSheetPart()->getId(), array_keys($values)))
                $values[$response->getCriterion()->getSheetPart()->getId()][] = $response->getResult();
            else
                $values[$response->getCriterion()->getSheetPart()->getId()] = [$response->getResult()];
        }
        foreach ($user->getInteviewsResultsAsCandidate() as $result) {
            $responses[$result->getRecruiters()->getId()]['decision'] = [
                'remark' => $result->getRemark(),
                'decision' => $result->getResult(),
            ];
        }
        $departments = $this->em->getRepository(Department::class)->findAll();
        $avgs = [];
        $countFinale = 0;
        $sumFinale = 0;
        foreach ($responses as $recruiterId => $candidateResults) {
            $recruiterSum = 0;
            $recruiterCoefCount = 0;
            foreach ($candidateResults as $sheetName => $results) {
                if ($sheetName != "decision") {
                    $sheetPart = $this->em->getRepository(InterviewsEvaluationSheetPart::class)->findOneBy(['name' => $sheetName]);
                    $sheetSum = 0;
                    $sheetCount = 0;
                    foreach ($results as $result) {
                        $sheetSum += $result["result"];
                        $sheetCount++;
                    }
                    $sheetMoy = ($sheetSum / $sheetCount);
                    $avgs[$recruiterId][$sheetName] = $sheetMoy;
                    $recruiterSum += $sheetMoy * $sheetPart->getCoefficient();
                    $recruiterCoefCount += $sheetPart->getCoefficient();
                }
            }
            $avgs[$recruiterId]["moy"] = $recruiterSum / $recruiterCoefCount;
            $sumFinale += $recruiterSum / $recruiterCoefCount;
            $countFinale++;
        }
        if ($countFinale > 0) {
            $moy = $sumFinale / $countFinale;
            $status = 'haveValues';
        } else {
            $moy = 0;
            $status = 'dontHaveValues';

        }
        $template = $this->render(
            'individual_interview\result_data.html.twig',
            [
                'responses' => $responses,
                'user' => $user,
                'average' => $moy,
                'departments' => $departments,
                'status' => $status,
                'avgs' => $avgs
            ]
        );
        return new JsonResponse([
            'template' => $template->getContent(),
            'log' => 'test test',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/interview/result/submit', name: 'app_individual_registration_user_result', methods: ['post'])]
    public function postUserResult(Request $request, MailerInterface $mailer): Response
    {
        $data = $request->request->get("result");
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get('user')]);

        if ($data == 'accept') {
            if ($user->getResult() != null) {
                $user->getResult()->setInterview(true);
                $dep = $this->em->getRepository(Department::class)->findOneBy([
                    'id' => $request->request->get('dep'),
                ]);
                $user->getResult()->setDepartmentChosen($dep);
            } else {
                $result = new Result();
                $result->setInterview(true);
                $dep = $this->em->getRepository(Department::class)->findOneBy([
                    'id' => $request->request->get('dep'),
                ]);
                $result->setDepartmentChosen($dep);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setInterviewEmail(false);
            $this->em->flush();
        } elseif ($data == 'refuse') {
            if ($user->getResult() != null) {
                $user->getResult()->setInterview(false);
                $user->getResult()->setDepartmentChosen(null);
            } else {
                $result = new Result();
                $result->setInterview(false);
                $result->setDepartmentChosen(null);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setInterviewEmail(false);
            $this->em->flush();
        } elseif ($data == "mail") {
            if ($user->getResult() != null) {
                if ($user->getResult()->getInterview() === true) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Interviews Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_accepted.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setInterviewEmail(true);
                } elseif ($user->getResult()->getInterview() === false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Interviews Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_refused.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setInterviewEmail(true);
                }
                $this->em->flush();
            } else {
                $data = 'mail_not_sent';
            }
        }
        return new JsonResponse(['result' => $data]);
    }

    //View Candidates/Interviews
    #[Route('recruiter/interviews/list', name: 'app_individual_interviews_list')]
    public function getInterviews(): Response
    {
        $allusers = [];
        foreach ($this->getUser()->getInterviews() as $Interview) {
            $allusers[] = $Interview->getCandidate();
        }
        $users = [];
        foreach ($allusers as $user) {
            $allowed = true;
            if ($user) {
                foreach ($user->getInterviewCriterionResults() as $result) {
                    if (
                        $result->getRecruiter()->getId() ==
                        $this->getUser()->getId()
                    ) {
                        $allowed = false;
                    }
                }
            }
            if ($allowed === true)
                $users[] = $user;
        }

        return $this->render('individual_interview\candidates.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('admin/interviews/calendar', name: 'app_interviews_redirect')]
    public function redirectToCalendar(): RedirectResponse
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $now = new DateTime();
        if ($currentRecruitmentSession) {
            if ($currentRecruitmentSession->getInterviewsScheduleEnd() > $now) {
                return $this->redirectToRoute('app_individual_interview');
            } elseif (($currentRecruitmentSession->getBookingForInterview() && $currentRecruitmentSession->getForBookingInterviewsScheduleEnd() > $now) || (!$currentRecruitmentSession->getBookingForInterview() && $currentRecruitmentSession->getValidateInterviewsScheduleEnd())) {
                return $this->redirectToRoute('app_individual_interview_create');
            } else {
                return $this->redirectToRoute('app_interview_emergency');
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }

    }
}
