<?php

namespace App\Action\Web;

use App\Domain\Defect\Service\DefectFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class DefectAction
{
    private $responder;
    private $twig;
    private $defectFinder;
    private $session;

    public function __construct(Twig $twig, DefectFinder $defectFinder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->defectFinder = $defectFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $viewData = [
            'defects' => $this->defectFinder->findDefects($params),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/defects.twig', $viewData);
    }
}
