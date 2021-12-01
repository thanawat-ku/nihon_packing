<?php

namespace App\Action\Web;

use App\Domain\Section\Service\SectionFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SectionAction
{
    private $responder;
    private $twig;
    private $sectionFinder;
    private $session;

    public function __construct(Twig $twig, SectionFinder $sectionFinder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->sectionFinder = $sectionFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $viewData = [
            'sections' => $this->sectionFinder->findSections($params),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/sections.twig', $viewData);
    }
}
