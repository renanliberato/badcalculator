<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

function getRandomBetweenZeroAndOne() {
    return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
}

class CalculateActionHandler implements RequestHandlerInterface {

    private $template;

    public function __construct(
            ?TemplateRendererInterface $template = null
    ) {
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $input = $request->getParsedBody()['input'];
        $session = $request->getAttribute('session');

        if ($input != null && $input != "") {
            $result = (new \App\Calculator())->calculate($input);

            $array = array('+', '-', '*', '/');
            $offset = 0;
            if (0 < count(array_intersect(array_map('strtolower', str_split($input)), $array))) {
                $offset = round((getRandomBetweenZeroAndOne() / (10 - strlen($input))), 2);
            }
            $session->set('result', $result + $offset);
        } else {
            $session->set('sent_no_values', true);
        }

        $session->set('input', $input);

        return new RedirectResponse("/badcalculator/");
    }

}
