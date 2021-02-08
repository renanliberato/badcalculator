<?php

declare(strict_types=1);

namespace App\Middlewares;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Session\SessionInterface;

class ThrottleMiddleware implements MiddlewareInterface
{
    const LAST_CALCULATION_NAME = 'last_calculation';
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $now = time();
        
        $session = $request->getAttribute("session");
        if ($session->has(ThrottleMiddleware::LAST_CALCULATION_NAME)) {
            $last = $session->get(ThrottleMiddleware::LAST_CALCULATION_NAME);
            if ($now - $last <= 5) {
                $session->set('tried_too_early', $last + 5);
                
                return new RedirectResponse('/badcalculator/');
            }
        }
        
        $session->set(ThrottleMiddleware::LAST_CALCULATION_NAME, $now);
        
        $response = $handler->handle($request);
        
        return $response;
    }
}
