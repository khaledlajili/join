<?php

namespace App\EventSubscriber;


use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use App\Entity\User;


class CheckVerifiedUserSubscriber implements EventSubscriberInterface

{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFailure'
        ];

    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }

        $response = new RedirectResponse(
            $this->router->generate('verify_user_resend_email', ['user' => $event->getPassport()->getUser()->getId()])
        );

        $event->setResponse($response);
    }


    public function onCheckPassport(CheckPassportEvent $event)

    {
        $passport = $event->getPassport();

        $user = $passport->getUser();

        if ($user instanceof User) {
            if (!$user->isIsVerified()) {
                throw new AccountNotVerifiedAuthenticationException();
            }

            if ($user->isRefused()) {
                throw new CustomUserMessageAuthenticationException(
                    'Your application is refused, you cannot access your account.'
                );
            }
            if (!(in_array('ROLE_ADMIN', $user->getRoles())) && $user->getRecruitmentSession()->getCurrent() !== true ) {
                throw new CustomUserMessageAuthenticationException(
                    'Your recruitment session is already finished.'
                );
            }
        }
    }
}


