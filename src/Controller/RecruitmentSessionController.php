<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\PreRegistrationFormField;
use App\Entity\RecruitmentSession;
use App\Entity\StudyLevel;
use App\Entity\TechnicalTest;
use App\Form\DepartmentType;
use App\Form\RecruitmentSessionType;
use App\Form\StudyLevelType;
use App\service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class RecruitmentSessionController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/admin/session', name: 'app_recruitment_session')]
    public function create(Request $request, UploaderHelper $uploaderHelper): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]) !== null ? $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]) : new RecruitmentSession();
        $preRegistrationFormFieldsNbr = count($this->em->getRepository(PreRegistrationFormField::class)->findBy(['recruitmentSession' => $recruitmentSession]));

        $startSessionForm = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('submit', SubmitType::class, [
                'label' => 'Start session'
            ])
            ->getForm();
        $startSessionForm->handleRequest($request);
        if ($startSessionForm->isSubmitted() && $startSessionForm->isValid()) {
            $recruitmentSession->setStart(new \DateTime());
            $this->em->persist($recruitmentSession);
            $this->em->flush();
        }

        $recruitmentSessionForm = $this->createForm(RecruitmentSessionType::class, $recruitmentSession);
        $department = new Department();
        $departmentForm = $this->createForm(DepartmentType::class, $department);
        $departmentForm->handleRequest($request);
        if ($departmentForm->isSubmitted() && $departmentForm->isValid()) {
            $this->em->persist($department);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                $departmentRow = $this->renderView('recruitment_session/_department_row.html.twig', [
                    'department' => $department
                ]);
                $department = new Department();
                $departmentForm = $this->createForm(DepartmentType::class, $department);
                $departmentFormView = $this->renderView('recruitment_session/_department_form.html.twig', [
                    'departmentForm' => $departmentForm->createView()
                ]);
                return new JsonResponse(['departmentFormView' => $departmentFormView, 'departmentRow' => $departmentRow], 200);
            }

            return $this->redirectToRoute('app_recruitment_session');

        } else {
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('recruitment_session/_department_form.html.twig', [
                    'departmentForm' => $departmentForm->createView()
                ]);
                return new Response($html, 400);
            }
        }
        $studyLevelForm = $this->createForm(StudyLevelType::class);


        $recruitmentSessionForm->handleRequest($request);

        if ($recruitmentSessionForm->isSubmitted() && $recruitmentSessionForm->isValid()) {
            $this->em->persist($recruitmentSession);
            $this->em->flush();
            return $this->redirectToRoute('app_recruitment_session');
        }
        return $this->render('recruitment_session/index.html.twig', [
            'currentRecruitmentSession' => $currentRecruitmentSession,
            'recruitmentSession' => $recruitmentSession,
            'preRegistrationFormFieldsNbr' => $preRegistrationFormFieldsNbr,
            'departments' => $this->em->getRepository(Department::class)->findAll(),
            'studyLevels' => $this->em->getRepository(StudyLevel::class)->findAll(),
            'departmentForm' => $departmentForm->createView(),
            'recruitmentSessionForm' => $recruitmentSessionForm->createView(),
            'studyLevelForm' => $studyLevelForm->createView(),
            'startSessionForm' => $startSessionForm->createView()
        ]);
    }

    #[Route('/admin/session/close', name: 'app_recruitment_session_close')]
    public function close(): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if (($currentRecruitmentSession->getTrialPeriod() && $currentRecruitmentSession->getTrialPeriodSelectionEnd() < new \DateTime()) || (!$currentRecruitmentSession->getTrialPeriod() && $currentRecruitmentSession->getInterviewsSelectionEnd())) {
            $currentRecruitmentSession->setCurrent(false);
            $this->em->flush();
            return $this->redirectToRoute('app_admin');
        }
        $this->addFlash('error','Youn can\'t end this session');
        return $this->redirectToRoute('app_admin');
    }


    #[Route('/admin/study/level/add', name: 'app_study_level_add')]
    public function addStudyLevel(Request $request)
    {
        $studyLevel = new StudyLevel();
        $studyLevelForm = $this->createForm(StudyLevelType::class, $studyLevel);
        $studyLevelForm->handleRequest($request);
        if ($studyLevelForm->isSubmitted() && $studyLevelForm->isValid()) {
            $this->em->persist($studyLevel);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                $studyLevelRow = $this->renderView('recruitment_session/_study_level_row.html.twig', [
                    'studyLevel' => $studyLevel
                ]);
                $studyLevel = new StudyLevel();
                $studyLevelForm = $this->createForm(StudyLevelType::class, $studyLevel);
                $studyLevelFormView = $this->renderView('recruitment_session/_study_level_form.html.twig', [
                    'studyLevelForm' => $studyLevelForm->createView()
                ]);
                return new JsonResponse(['studyLevelFormView' => $studyLevelFormView, 'studyLevelRow' => $studyLevelRow], 200);
            }
            return $this->redirectToRoute('app_recruitment_session');
        } else {
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('recruitment_session/_study_level_form.html.twig', [
                    'studyLevelForm' => $studyLevelForm->createView()
                ]);
                return new Response($html, 400);
            }
        }
    }

    #[Route('/admin/department/remove/{department}', name: 'app_department_remove')]
    public function removeDepartment(Department $department): Response
    {
        $this->em->remove($department);
        $this->em->flush();

        return $this->redirectToRoute('app_recruitment_session');
    }


    #[Route('/admin/study/level/remove/{studyLevel}', name: 'app_study_level_remove')]
    public function removeStudyLevel(StudyLevel $studyLevel): Response
    {
        $this->em->remove($studyLevel);
        $this->em->flush();

        return $this->redirectToRoute('app_recruitment_session');
    }
}
