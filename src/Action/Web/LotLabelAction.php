<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  LotLabelAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;


    public function __construct(Twig $twig, LabelFinder $finder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $lotId = $data["id"];
        $params["lot_id"] = $lotId;

        $viewData = [
            'labels' => $this->finder->findLabels($params),
            'user_login' => $this->session->get('user'),
        ];
        return $this->twig->render($response, 'web/labels.twig', $viewData);
    }
}
