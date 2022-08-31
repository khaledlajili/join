<?php

namespace App\Controller;


use App\Entity\Department;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\StudyLevel;
use App\Entity\User;
use App\Repository\ResultRepository;
use App\Repository\StudyLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('admin/statistics/{recruitmentSession}', name: 'app_statistics')]
    public function index(Request $request,RecruitmentSession $recruitmentSession,StudyLevelRepository $sr,ResultRepository $rr): Response
    {              
        $users=[];
        $Studyfields=$sr->findAll();
        foreach($Studyfields as $field){
            $fields[$field->getId()]=$field->getName();
        }
        if($recruitmentSession->getTrialPeriod())
            $criteria = "trialPeriod";
        else
            $criteria = "interview";

        $successResults=$rr->findBy([$criteria=>1]);
        foreach($successResults as $result){
            $users[] = $result->getUser();
        }
        $exclu=$rr->findBy(["trialPeriod"=>0,"interview"=>1]);
        return $this->render('statistics/index.html.twig', [
            'recruitmentSession' => $recruitmentSession,
            'fields'=>$fields,
            'usersReussi'=>$users,
            'exclu'=>$exclu,
            'inscrits'=>$this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession)
        ]);
    }
    #[Route('admin/statistics/data/{id}', name: 'app_statistics_data')]
    public function data(Request $request,RecruitmentSession $recruitmentSession): Response
    {

        $sr=$this->em->getRepository(StudyLevel::class);
        $rr=$this->em->getRepository(Result::class);
        $dr=$this->em->getRepository(Department::class);
        if($request->request->has("totalReussiStudyField")){
            $Studyfields=$sr->findAll();
            $dataset=[ ["Candidates", "Total", "Succeeded"]];
            foreach($Studyfields as $field){
                $fieldUsers=$field->getUsers();
                $fieldUsers = \array_filter($fieldUsers->toArray(), static function ($user) {
                    return in_array("ROLE_CANDIDATE",$user->getRoles());
                });
                $reussi=[];
                foreach($fieldUsers as $user){
                    if($user->getResult()){
                        if($recruitmentSession->getTrialPeriod()){
                            if($user->getResult()->getTrialPeriod())
                                $reussi[] = $user;
                        }
                        else{
                            if($user->getResult()->getInterview())
                                $reussi[] = $user;
                        }
                    }
                }
                $dataset[] = [$field->getName(), count($fieldUsers), count($reussi)];
            }
            return new JsonResponse(["dataset"=>$dataset]);
        }

        if($request->request->has("quotaReussi")){

            $candidats = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
            $total= count($candidats);
            if($recruitmentSession->getTrialPeriod())
                $reussi = count($rr->findBy(["trialPeriod"=>true]));
            else
                $reussi = count($rr->findBy(["interview"=>true]));

            return new JsonResponse(["dataset"=>round((($reussi/$total)*100),1)]);
            
        }
        
        if($request->request->has("RepartitionDepReussi")){
            $dataset=[];$tempDataset=[];
            if($recruitmentSession->getTrialPeriod())
                $criteria = "trialPeriod";
            else
                $criteria = "interview";

            $Studyfields=$sr->findAll();
            $allDepartments=$dr->findAll();
            foreach($Studyfields as $field){
                $tempDataset[$field->getId()]=[];
                $tempDataset["all"]=[];
                $sfieldsarray[$field->getId()]=$field->getName();
                foreach($allDepartments as $department){
                    $tempDataset[$field->getId()][$department->getName()]=0;
                    $tempDataset["all"][$department->getName()]=0;
                }
                $tempDataset[$field->getId()]["total"]=0;
            }
            $successResults=$rr->findBy([$criteria=>1]);
            foreach($successResults as $result){
                $tempDataset[$result->getUser()->getStudyLevel()->getId()][$result->getDepartmentChosen()->getName()]++;
                $tempDataset[$result->getUser()->getStudyLevel()->getId()]["total"]++;
                $tempDataset["all"][$result->getDepartmentChosen()->getName()]++;
            }
            $tempDataset["all"]["total"]=count($successResults);
            foreach($tempDataset as $key=>$data){
                $dataset[$key]=[];
                foreach($data as $singlekey=>$singleData){
                    if($singlekey !="total"){
                        if($data["total"])
                            $dataset[$key][] = ["value" => round((($singleData / $tempDataset[$key]["total"]) * 100), 1), "name" => $singlekey];
                        else
                            $dataset[$key][] = ["value" => 0, "name" => $singlekey];
                    }
                }
            }
            return new JsonResponse(["dataset"=>$dataset]);


        }
        if($request->request->has("statSatisfaction")){
            $preRegistrationCount=0;
            $preRegistrationLength=0;
            $technicalTestCount=0;
            $technicalTestLength=0;
            $collectiveInterviewCount=0;
            $collectiveInterviewLength=0;
            $interviewCount=0;
            $interviewLength=0;
            $trialPeriodCount=0;
            $trialPeriodLength=0;
            foreach($this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession) as $user){
                if($user->getFeedback()){
                    if($user->getFeedback()->getPreRegistrationRating()){
                        $preRegistrationCount+=$user->getFeedback()->getPreRegistrationRating()/5;
                        $preRegistrationLength++;
                    }
                    if($user->getFeedback()->getTechnicalTestRating()){
                        $technicalTestCount+=$user->getFeedback()->getTechnicalTestRating()/5;
                        $technicalTestLength++;
                    }
                    if($user->getFeedback()->getCollectiveInterviewsRating()){
                        $collectiveInterviewCount+=$user->getFeedback()->getCollectiveInterviewsRating()/5;
                        $collectiveInterviewLength++;
                    }
                    if($user->getFeedback()->getIndividualInterviewsRating()){
                        $interviewCount+=$user->getFeedback()->getIndividualInterviewsRating()/5;
                        $interviewLength++;
                    }
                    if($user->getFeedback()->getTrialPeriodRating()){
                        $trialPeriodCount+=$user->getFeedback()->getTrialPeriodRating()/5;
                        $trialPeriodLength++;
                    }
                }
            }

            if($preRegistrationLength>0)
                $dataset['preRegistration']=($preRegistrationCount/$preRegistrationLength)*100;
            else
                $dataset['preRegistration']=0;

            if($technicalTestLength>0)
                $dataset['technicalTest']=($technicalTestCount/$technicalTestLength)*100;
            else
                $dataset['technicalTest']=0;

            if($collectiveInterviewLength>0)
                $dataset['collectiveInterview']=($collectiveInterviewCount/$collectiveInterviewLength)*100;
            else
                $dataset['collectiveInterview']=0;

            if($interviewLength>0)
                $dataset['interview']=($interviewCount/$interviewLength)*100;
            else
                $dataset['interview']=0;

            if($trialPeriodLength>0)
                $dataset['trialPeriod']=($trialPeriodCount/$trialPeriodLength)*100;
            else
                $dataset['trialPeriod']=0;

            $dataset['satisfaction']=array_sum($dataset)/5;
            return new JsonResponse(["dataset"=>$dataset]);
        }
    }
}
