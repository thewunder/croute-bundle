<?php

namespace Croute\CrouteBundle;

use Croute\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CrouteKernel implements HttpKernelInterface
{
    public function __construct(private readonly Router $router)
    {
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        return $this->router->route($request);
    }
}
