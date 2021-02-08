<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Plates\PlatesRenderer;
use Mezzio\Router;
use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Twig\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    /** @var string */
    private $containerName;

    /** @var Router\RouterInterface */
    private $router;

    /** @var null|TemplateRendererInterface */
    private $template;

    public function __construct(
        string $containerName,
        Router\RouterInterface $router,
        ?TemplateRendererInterface $template = null
    ) {
        $this->containerName = $containerName;
        $this->router        = $router;
        $this->template      = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $session = $request->getAttribute("session");
        $data = [
            'input' => $session->get("input", ""),
            'sentNoValues' => $session->get("sent_no_values", false),
            'triedToEarly' => $session->has('tried_too_early') ? $session->get('tried_too_early') - time() : null,
            'result' => $session->get('result', null)
        ];
        
        if ($data['result'] != null && !$session->has('tried_too_early')) {
            $session->unset('input');
        }
        
        $session->unset('tried_too_early');
        $session->unset('sent_no_values');
        $session->unset('result');
        
        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
}
