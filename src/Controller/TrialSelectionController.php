<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class TrialSelectionController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('admin/trial/result', name: 'app_trial_result')]
    public function index(): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $users = [];
        if ($recruitmentSession) {
            if (!$recruitmentSession->getTrialPeriod()){
                $this->addFlash('error',"There's no trial period in this session");
                return $this->redirectToRoute('app_admin');
            }
            $allusers = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
            $departments = $this->em->getRepository(Department::class)->findAll();
            foreach ($allusers as $user) {
                if ($user->getResult() && $user->getResult()->getInterview() === true) {
                    $users[] = $user;
                }
            }
        } else {
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('trial_selection/index.html.twig', [
            'users' => $users,
            'departments' => $departments,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/trial/result/submit', name: 'app_trial_selection_user_result', methods: ['post'])]
    public function postUserResult(Request $request, MailerInterface $mailer): Response
    {
        $data = $request->request->get("result");
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get('user')]);
        if ($data == 'accept') {
            if ($user->getResult() != null) {
                $user->getResult()->setTrialPeriod(true);
            } else {
                $result = new Result();
                $result->setTrialPeriod(true);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setTrialPeriodEmail(false);
            $this->em->flush();
        } elseif ($data == 'refuse') {
            if ($user->getResult() != null) {
                $user->getResult()->setTrialPeriod(false);
            } else {
                $result = new Result();
                $result->setTrialPeriod(false);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setTrialPeriodEmail(false);
            $this->em->flush();
        } elseif ($data == "mail") {
            if ($user->getResult() != null) {
                if ($user->getResult()->getInterview() === true) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Trial Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_accepted.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setTrialPeriodEmail(true);
                } elseif ($user->getResult()->getInterview() === false) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName() . ' ' . $user->getLName()))
                        ->subject('[ Trial Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_refused.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setTrialPeriodEmail(true);
                }
                $this->em->flush();
            } else {
                $data = 'mail_not_sent';
            }
        }
        return new JsonResponse(['result' => $data]);
    }
}
