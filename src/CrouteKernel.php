<?php

namespace Croute\CrouteBundle;

use Croute\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CrouteKernel implements HttpKernelInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly RequestStack $requestStack,
        private readonly SessionFactoryInterface $sessionFactory
    )
    {
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        $this->requestStack->push($request);

        try {
            if($type == self::MAIN_REQUEST) {
                $this->startSession($request);
            }
            $response = $this->router->route($request);
        } finally {
            $this->requestStack->pop();
        }
        return $response;
    }

    private function startSession(Request $request)
    {
        if (!$request->hasSession()) {
            $session = $this->sessionFactory->createSession();
            $session->start();
            $request->setSession($session);
        }
    }
}
