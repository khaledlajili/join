<?php

namespace App\Controller;

use App\Entity\RecruitmentSession;
use App\Form\AdminRegistrationType;
use App\Form\RecruiterRegistrationType;
use App\service\UploaderHelper;
use Exception;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private EntityManagerInterface $em;
    private MailerInterface $mailer;

    public function __construct(VerifyEmailHelperInterface $helper, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UploaderHelper $uploaderHelper): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if ($currentRecruitmentSession) {
            $user = new User();
            $registrationForm = $this->createForm(RegistrationType::class, $user);
            $registrationForm->handleRequest($request);

            if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
                try {
                    // encode the plain password
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $registrationForm->get('password')->getData()
                        )
                    );
                    if ($registrationForm['img']->getData()) {
                        /** @var UploadedFile $uploadedFile */
                        $uploadedFile = $registrationForm['img']->getData();
                        $newFilename = $uploaderHelper->uploadCandidateImg($uploadedFile);
                        $user->setImg($newFilename);
                    } else {
                        $user->setImg("default.jpg");
                    }
                    $user->setRecruitmentSession($currentRecruitmentSession);
                    $user->setIsVerified(false);

                } catch (Exception $e) {
                    die($e->getMessage());
                } catch (FilesystemException $e) {
                }

                $this->em->persist($user);
                $this->em->flush();

                // do anything else you need here, like send an email
                $signatureComponents = $this->verifyEmailHelper->generateSignature(
                    'verify_user_email',
                    $user->getId(),
                    $user->getEmail(),
                    ['id' => $user->getId()]
                );
                $this->addFlash('success', 'Please confirm your email address in order to access your account.');
                $this->addFlash('info', 'If you don\'t receive anything in your inbox, please check your spam box.');
                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                    ->subject('[ Verify your email address ]')
                    ->htmlTemplate('email/verify_email.html.twig')
                    ->context([
                        'signatureComponents' => $signatureComponents,
                        'user' => $user
                    ]);
                $this->mailer->send($email);

                return $this->redirectToRoute('app_login');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $registrationForm->createView(),
                'currentRecruitmentSession' => $currentRecruitmentSession
            ]);
        } else {
            return $this->render('registration/register.html.twig', [
                'currentRecruitmentSession' => $currentRecruitmentSession
            ]);
        }
    }

    #[Route('/verify', name: 'verify_user_email')]
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        $user = $this->em->getRepository(User::class)->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_login');
        }
        $user->setIsVerified(true);
        $this->em->flush();
        $this->addFlash('success', 'Your email address has been verified. You can now login into your account.');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/verify/resend/{user}', name: 'verify_user_resend_email')]
    public function resendVerifyUserEmail(User $user): RedirectResponse
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'verify_user_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );
        $this->addFlash('success', 'A verification email has been resent to you.');
        $email = (new TemplatedEmail())
            ->from(new Address('email@your-domain.com', 'JOIN'))
            ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
            ->subject('[ Verify your E-mail address ][ Re-sent ]')
            ->htmlTemplate('email/verify_email.html.twig')
            ->context([
                'signatureComponents' => $signatureComponents,
                'user' => $user
            ]);
        $this->mailer->send($email);

        return $this->redirectToRoute('app_login');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/admin/recruiter/register', name: 'app_recruiter_register')]
    public function registerRecruiter(Request $request, UserPasswordHasherInterface $userPasswordHasher, UploaderHelper $uploaderHelper): Response
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if ($currentRecruitmentSession) {
            $recruiter = new User();
            $recruiterRegistrationForm = $this->createForm(RecruiterRegistrationType::class, $recruiter);
            $recruiterRegistrationForm->handleRequest($request);
            if ($recruiterRegistrationForm->isSubmitted() && $recruiterRegistrationForm->isValid()) {
                $plainPassword = $recruiterRegistrationForm->get('password')->getData();
                try {
                    // encode the plain password
                    $recruiter->setPassword(
                        $userPasswordHasher->hashPassword(
                            $recruiter,
                            $plainPassword
                        )
                    );
                    if ($recruiterRegistrationForm['img']->getData()) {
                        /** @var UploadedFile $uploadedFile */
                        $uploadedFile = $recruiterRegistrationForm['img']->getData();
                        $newFilename = $uploaderHelper->uploadCandidateImg($uploadedFile);
                        $recruiter->setImg($newFilename);
                    } else {
                        $recruiter->setImg("default.jpg");
                    }
                    $recruiter->setRoles(["ROLE_RECRUITER"]);
                    $recruiter->setRecruitmentSession($currentRecruitmentSession);
                    $recruiter->setIsVerified(true);

                } catch (Exception $e) {
                    die($e->getMessage());
                } catch (FilesystemException $e) {
                }

                $this->em->persist($recruiter);
                $this->em->flush();

                // do anything else you need here, like send an email


                $email = (new TemplatedEmail())
                    ->from(new Address('email@your-domain.com', 'JOIN'))
                    ->to(new Address($recruiter->getEmail(), $recruiter->getFName() . ' ' . $recruiter->getLName()))
                    ->subject('[ Your recruiter account credentials ]')
                    ->htmlTemplate('email/register_recruiter_email.html.twig')
                    ->context([
                        'recruiter' => $recruiter,
                        'plainPassword' => $plainPassword
                    ]);
                $this->mailer->send($email);

                if ($request->isXmlHttpRequest()) {
                    $recruiterRow = $this->renderView('registration/_recruiter_row.html.twig', [
                        'recruiter' => $recruiter
                    ]);

                    $recruiter = new User();
                    $recruiterRegistrationForm = $this->createForm(RecruiterRegistrationType::class, $recruiter);
                    $recruiterRegistrationFormView = $this->renderView('registration/_recruiter_form.html.twig', [
                        'recruiterRegistrationForm' => $recruiterRegistrationForm->createView()
                    ]);
                    return new JsonResponse(['recruiterRegistrationFormView' => $recruiterRegistrationFormView, 'recruiterRow' => $recruiterRow], 200);
                }
            } else {
                if ($request->isXmlHttpRequest()) {
                    $html = $this->renderView('registration/_recruiter_form.html.twig', [
                        'recruiterRegistrationForm' => $recruiterRegistrationForm->createView()
                    ]);
                    return new Response($html, 400);
                }
            }
            $recruiters = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_RECRUITER', $currentRecruitmentSession);
        } else {
            $this->addFlash('error', 'You must add a recruitment session before adding recruiters.');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('registration/register_recruiter.html.twig', [
            'recruiterRegistrationForm' => $recruiterRegistrationForm->createView(),
            'recruitmentSession' => $currentRecruitmentSession,
            'recruiters' => $recruiters
        ]);
    }

    #[Route('/register/admin', name: 'app_admin_register')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, UploaderHelper $uploaderHelper): Response
    {
        $admin = new User();
        $adminRegistrationForm = $this->createForm(AdminRegistrationType::class, $admin);
        $adminRegistrationForm->handleRequest($request);
        if ($adminRegistrationForm->isSubmitted() && $adminRegistrationForm->isValid()) {
            $plainPassword = $adminRegistrationForm->get('password')->getData();
            try {
                // encode the plain password
                $admin->setPassword(
                    $userPasswordHasher->hashPassword(
                        $admin,
                        $plainPassword
                    )
                );
                $admin->setRoles(["ROLE_ADMIN"]);
                $admin->setIsVerified(true);

            } catch (Exception $e) {
                die($e->getMessage());
            } catch (FilesystemException $e) {
            }

            $this->em->persist($admin);
            $this->em->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register_admin.html.twig', [
            'adminRegistrationForm' => $adminRegistrationForm->createView()
        ]);
    }

    #[Route('/admin/recruiter/remove/{recruiter}', name: 'app_recruiter_remove')]
    public function removeRecruiter(User $recruiter): Response
    {
        $this->em->remove($recruiter);
        $this->em->flush();

        return $this->redirectToRoute('app_recruiter_register');
    }
}
