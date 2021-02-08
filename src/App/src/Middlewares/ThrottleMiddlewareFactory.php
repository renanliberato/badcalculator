<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Container\ContainerInterface;
use Mezzio\Session\SessionInterface;

class ThrottleMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : ThrottleMiddleware
    {
        return new ThrottleMiddleware();
    }
}
