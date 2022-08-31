<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\RecruitmentSession;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_landing')]
    public function landing(): Response
    {
        return $this->render('main/landing.html.twig');
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        return $this->render('main/admin.html.twig', [
            'recruitmentSessions' => $this->em->getRepository(RecruitmentSession::class)->findAll(),
            'currentRecruitmentSession' => $currentRecruitmentSession
        ]);
    }

    #[Route('/admin/candidates/{recruitmentSession}', name: 'app_admin_candidates')]
    public function candidates(RecruitmentSession $recruitmentSession): Response
    {
        return $this->render('main/candidates.html.twig', [
            'users' => $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession),
            'recruitmentSession'=>$recruitmentSession,
            'departments'=> $this->em->getRepository(Department::class)->findAll()
        ]);
    }

    #[Route('/admin/candidates/details/{user}', name: 'app_admin_candidate_details')]
    public function candidateDetails(User $user): Response
    {
        $template = $this->render(
            'main\candidate_details.html.twig',
            [
                'user' => $user,
            ]
        );
        return new JsonResponse([
            'template' => $template->getContent(),
            'log' => 'test test',
        ]);
    }

    #[Route('/candidate', name: 'app_candidate')]
    public function index(): Response
    {
        $today = new DateTime();

        $recruitementSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current'=>true]);
        $currentUser=$this->getUser();

        //Init
        $state='preRegOpen';
        $calendarstate='cannotAccessCalendar';
        //PreReg phase
        if($today > $recruitementSession->getStart() && $today < $recruitementSession->getRegistrationEnd()){
            if($currentUser->getResponses()->get(0) != null){
                $state='preRegSubmitted';
            }
        }
        else
        {
            $state='preRegClosed';
        }

        if($currentUser->getResult() && $currentUser->getResult()->getPreRegistration()){
            //2 cases: with collective / without collective

            //with collective
            if($recruitementSession->getCollectiveInterview()){
                $state='pendingCollectiveDate';
                // dd($currentUser->getCollectiveInterview());
                if($currentUser->getCollectiveInterview()){
                    $state="hasCollectiveInterview";
                }
                if($currentUser->getResult()->getCollectiveInterviews()){
                    //2 cases: with technical / without technical

                    //with technical
                    if($recruitementSession->getTechnicalTest()){
                        $state='testPeriodStart';
                        if($today > $recruitementSession->getCollectiveInterviewsSelectionEnd() && $today < $recruitementSession->getTechnicalTestEnd()){
                            if($currentUser->getTechnicalTestResult() != null){
                                $state='testSubmitted';
                            }
                        }
                        else{
                            $state='testPeriodEnd';
                        }
                        
                        //if succeded
                        if($currentUser->getResult()->getTechnicalTestResult()){
                            $state='readyForInterview';
                        }
                    }
                    //without technical
                    else{
                        $state='readyForInterview';
                    }
                }
            }
            //Without collective
            else{
                //with technical
                if($recruitementSession->getTechnicalTest()){
                    $state='testPeriodStart';
                    if($today > $recruitementSession->getPreRegistrationSelectionEnd() && $today < $recruitementSession->getTechnicalTestEnd()){
                        if($currentUser->getTechnicalTestResult() != null){
                            $state='testSubmitted';
                        }
                    }
                    else{
                        $state='testPeriodEnd';
                    }
                    
                    //if succeded
                    if($currentUser->getResult()->getTechnicalTestResult()){
                        $state='readyForInterview';
                    }
                }
                //without technical
                else{
                    $state='readyForInterview';
                }
            }
        }

        if($state == 'readyForInterview' ){
            //2 cases : can Book / cannot Book

            //can Book
            if($recruitementSession->getBookingForInterview()){
                if($today > $recruitementSession->getForBookingInterviewsScheduleEnd() && $today < $recruitementSession->getValidateInterviewsScheduleEnd()){
                    $calendarstate='canAccessCalendar';
                }
                if($currentUser->getInterview()){
                    $state='hasInterview';
                }
            }
            //cannot Book
            else{
                if($currentUser->getInterview()){
                    $state='hasInterview';
                }
            }   
        }
        if($currentUser->getResult() && $currentUser->getResult()->getInterview()){
            //with trial
            if($recruitementSession->getTrialPeriod()){
                $state='trialPeriod';
                if($currentUser->getResult() && $currentUser->getResult()->getTrialPeriod()){
                    $state='accepted';
                }
            }
            else{
                $state='accepted';
            }
        }
        
        $steps=2+$recruitementSession->getTechnicalTest()+$recruitementSession->getCollectiveInterview()+$recruitementSession->getTrialPeriod();
        $stepReached= $currentUser->getResult() == null ? 0 : ($currentUser->getResult()->getPreRegistration()&&$currentUser->getResult()->isPreRegistrationEmail()) + ($currentUser->getResult()->getCollectiveInterviews()&& $recruitementSession->getCollectiveInterview()&&$currentUser->getResult()->isCollectiveInterviewsEmail()) +($currentUser->getResult()->getTechnicalTestResult()&& $recruitementSession->getTechnicalTest()&&$currentUser->getResult()->isTechnicalTestEmail()) + ($currentUser->getResult()->getInterview()&&$currentUser->getResult()->isInterviewEmail()) + ($currentUser->getResult()->getTrialPeriod()&& $recruitementSession->getTrialPeriod()&&$currentUser->getResult()->isTrialPeriodEmail())   ;
        $pourcentage=($stepReached/$steps)*100;

        return $this->render('main/index.html.twig', [
            'pourcentage'=>$pourcentage,
            'user'=>$currentUser,
            'state'=>$state,
            'calendarState'=>$calendarstate,
            'recruitmentSession'=>$recruitementSession
        ]);
    }

    #[Route('/admin/results', name: 'app_results')]
    public function resultPage(): Response
    {
        $today = new DateTime();
        $recruitementSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if($recruitementSession){
            $state=[];
            if($today > $recruitementSession->getPreRegistrationSelectionEnd())
                $state['preReg']=true;
            else
                $state['preReg']=false;

            if( $recruitementSession->getCollectiveInterview() && $today>$recruitementSession->getCollectiveInterviewsSelectionEnd())
                $state['collective']=true;
            else
                $state['collective']=false;

            if( $recruitementSession->getTechnicalTest() && $today>$recruitementSession->getTechnicalTestSelectionEnd())
                $state['technical']=true;
            else
                $state['technical']=false;

            if($today>$recruitementSession->getInterviewsSelectionEnd())
                $state['individual']=true;
            else
                $state['individual']=false;

            if( $recruitementSession->getTrialPeriod() && $today>$recruitementSession->getTrialPeriodSelectionEnd())
                $state['trial']=true;
            else
                $state['trial']=false;

            $steps=2+$recruitementSession->getTechnicalTest()+$recruitementSession->getCollectiveInterview()+$recruitementSession->getTrialPeriod();
            $stepReached= $steps - count(array_filter($state));
            $pourcentage=($stepReached/$steps)*100;
            return $this->render('main/result.html.twig', [
                'pourcentage'=> $pourcentage,
                'user'=> $this->getUser(),
                'state'=>$state,
                'calendarState'=>'',
                'recruitmentSession'=>$recruitementSession
            ]);
        }
        else{
            return $this->render('main/result.html.twig', [
                'pourcentage'=>'',
                'user'=>'',
                'state'=>'',
                'calendarState'=>'',
                'recruitmentSession'=>$recruitementSession
            ]);
        }
    }

    #[Route('/recruiter', name: 'app_recruiter')]
    public function recruiterHello(): Response
    {
        $today = new DateTime();
        $recruitementSession= $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current'=>true]);
        if($recruitementSession){
            $state=[];
            if($today > $recruitementSession->getPreRegistrationSelectionEnd())
                $state['preReg']=true;
            else
                $state['preReg']=false;
    
            if( $recruitementSession->getCollectiveInterview() && $today>$recruitementSession->getCollectiveInterviewsSelectionEnd())
                $state['collective']=true;
            else
                $state['collective']=false;
    
            if( $recruitementSession->getTechnicalTest() && $today>$recruitementSession->getTechnicalTestSelectionEnd())
                $state['technical']=true;
            else
                $state['technical']=false;
    
            if($today>$recruitementSession->getInterviewsSelectionEnd())
                $state['individual']=true;
            else
                $state['individual']=false;
    
            if( $recruitementSession->getTrialPeriod() && $today>$recruitementSession->getTrialPeriodSelectionEnd())
                $state['trial']=true;
            else
                $state['trial']=false;
    
            $steps=2+$recruitementSession->getTechnicalTest()+$recruitementSession->getCollectiveInterview()+$recruitementSession->getTrialPeriod();
            $stepReached= $steps - count(array_filter($state));
            $pourcentage=($stepReached/$steps)*100;
            return $this->render('main/recruiter.html.twig', [
                'pourcentage'=>$pourcentage,
                'user'=>$this->getUser(),
                'state'=>$state,
                'calendarState'=>'',
                'recruitmentSession'=>$recruitementSession
            ]);
        }
        else{
            return $this->render('main/recruiter.html.twig', [
                'pourcentage'=>'',
                'user'=>'',
                'state'=>'',
                'calendarState'=>'',
                'recruitmentSession'=>$recruitementSession
            ]);
        }
    }
}
