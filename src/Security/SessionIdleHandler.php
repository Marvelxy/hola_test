<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class SessionIdleListener
{
    /**
     * @var int
     */
    private $maxIdleTime;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    public function __construct(
        string $maxIdleTime,
        Session $session,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        AuthorizationCheckerInterface $checker
    ) {
        $this->maxIdleTime = (int) $maxIdleTime;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->checker = $checker;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()
            || $this->maxIdleTime <= 0
            || $this->isAuthenticatedAnonymously()) {
            return;
        }

        $session = $this->session;
        $session->start();

        if ((time() - $session->getMetadataBag()->getLastUsed()) <= $this->maxIdleTime) {
            return;
        }

        $this->tokenStorage->setToken();
        $session->getFlashBag()->set('info', 'You have been logged out due to inactivity.');

        $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
    }

    private function isAuthenticatedAnonymously(): bool
    {
        return !$this->tokenStorage->getToken()
            || !$this->checker->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY);
    }
}
