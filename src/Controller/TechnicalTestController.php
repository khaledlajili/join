<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\TechnicalTest;
use App\Entity\TechnicalTestResult;
use App\Entity\User;
use App\Form\TechnicalTestResultType;
use App\Form\TechnicalTestType;
use App\service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TechnicalTestController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws FilesystemException
     */
    #[Route('/admin/test/add', name: 'app_technical_test_add')]
    public function addTechnicalTests(UploaderHelper $uploaderHelper, Request $request): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $technicalTest = new TechnicalTest();
        $technicalTestForm = $this->createForm(TechnicalTestType::class, $technicalTest);
        $technicalTestForm->handleRequest($request);
        if ($technicalTestForm->isSubmitted() && $technicalTestForm->isValid()) {
            $department = $this->em->getRepository(Department::class)->find($technicalTestForm['department']->getData());
            $oldTechnicalTest = $this->em->getRepository(TechnicalTest::class)->findOneBy(['recruitmentSession' => $currentRecruitmentSession, 'department' => $department]);
            if ($oldTechnicalTest) {
                $this->em->remove($oldTechnicalTest);
            }
            $technicalTest->setDepartment($department);
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $technicalTestForm['pdf']->getData();
            if ($oldTechnicalTest) {
                $newFilename = $uploaderHelper->uploadTechnicalTest($uploadedFile, $oldTechnicalTest->getPdf());
            } else {
                $newFilename = $uploaderHelper->uploadTechnicalTest($uploadedFile);
            }
            $technicalTest->setPdf($newFilename);
            $technicalTest->setRecruitmentSession($currentRecruitmentSession);

            $this->em->persist($technicalTest);
            $this->em->flush();
        }
        return $this->render('technical_test/add_technical_test.html.twig', [
            'currentRecruitmentSession' => $currentRecruitmentSession,
            'departments' => $this->em->getRepository(Department::class)->findAll(),
            'technicalTestForm' => $technicalTestForm
        ]);
    }

    /**
     * @throws FilesystemException
     */
    #[Route('/candidate/test/submit', name: 'app_technical_test_submit')]
    public function submitTechnicalTest(UploaderHelper $uploaderHelper, Request $request): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $technicalTestResult = new TechnicalTestResult();
        $technicalTestResult->setCandidate($this->getUser());
        $technicalTestResultForm = $this->createForm(TechnicalTestResultType::class, $technicalTestResult);
        $technicalTestResultForm->handleRequest($request);
        if ($technicalTestResultForm->isSubmitted() && $technicalTestResultForm->isValid()) {
            $technicalTest = $this->em->getRepository(TechnicalTest::class)->find($technicalTestResultForm['technicalTest']->getData());
            $technicalTestResult->setTechnicalTest($technicalTest);
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $technicalTestResultForm['file']->getData();
            $newFilename = $uploaderHelper->uploadTechnicalTestResult($uploadedFile);
            $technicalTestResult->setFilename($newFilename);

            $this->em->persist($technicalTestResult);
            $this->em->flush();
        }
        return $this->render('technical_test/submit_technical_test.html.twig', [
            'currentRecruitmentSession' => $currentRecruitmentSession,
            'departments' => $this->em->getRepository(Department::class)->findAll(),
            'technicalTestResultForm' => $technicalTestResultForm
        ]);
    }

    #[Route('/test/view/{technicalTest}', name: 'app_technical_test_view')]
    public function downloadTechnicalTest($technicalTest, UploaderHelper $uploaderHelper): StreamedResponse
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }
        $response = new StreamedResponse(function () use ($technicalTest, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream('technical_tests/' . $technicalTest, false);
            stream_copy_to_stream($fileStream, $outputStream);
        });
        $response->headers->set('content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/admin/test/result/view/{technicalTest}', name: 'app_technical_test_result_view')]
    public function downloadTechnicalTestResult($technicalTest, UploaderHelper $uploaderHelper): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($technicalTest, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream('technical_tests_results/' . $technicalTest, false);
            stream_copy_to_stream($fileStream, $outputStream);
        });
        $response->headers->set('content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/admin/test/result', name: 'app_technical_test_result')]
    public function getResponse(): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $users = [];
        if ($recruitmentSession) {
            if (!$recruitmentSession->getTechnicalTest()){
                $this->addFlash('error',"There's no technical test in this session");
                return $this->redirectToRoute('app_admin');
            }
            $allusers = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
            foreach ($allusers as $user) {
                if ($user->getResult() && ((!$recruitmentSession->getCollectiveInterviews() && $user->getResult()->getPreRegistration()) || $user->getResult()->getCollectiveInterviews())) {
                    $users[] = $user;
                }
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }


        return $this->render('technical_test/result.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('admin/test/result/data/{user}', name: 'app_test_single_response', methods: ['post'])]
    public function getResultData(User $user): Response
    {
        $allowed = false;
        if ($user->getTechnicalTestResults()->get(0) != null) {
            $allowed = true;
        }
        $template = $this->render(
            'technical_test/result_data.html.twig',
            ['user' => $user, 'allowed' => $allowed]
        );
        return new JsonResponse([
            'template' => $template->getContent()
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/test/result/submit', name: 'app_test_user_result', methods: ['post'])]
    public function postUserResult(Request $request, MailerInterface $mailer): Response
    {
        $data = $request->request->get("result");
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get('user')]);
        if ($data == 'accept') {
            if ($user->getResult() != null) {
                $user->getResult()->setTechnicalTestResult(true);
            } else {
                $result = new Result();
                $result->setTechnicalTestResult(true);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setTechnicalTestEmail(false);
            $this->em->flush();
        } elseif ($data == 'refuse') {
            if ($user->getResult() != null) {
                $user->getResult()->setTechnicalTestResult(false);
            } else {
                $result = new Result();
                $result->setTechnicalTestResult(false);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setTechnicalTestEmail(false);
            $this->em->flush();
        } elseif ($data == "mail") {
            if ($user->getResult() != null) {
                if ($user->getResult()->getTechnicalTestResult() === true) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Technical Test Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_accepted.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setTechnicalTestEmail(true);
                } elseif ($user->getResult()->getTechnicalTestResult() === false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Technical Test Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_refused.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setTechnicalTestEmail(true);
                }
                $this->em->flush();
            } else {
                $data = 'mail_not_sent';
            }
        }
        return new JsonResponse(['result' => $data]);
    }

}
