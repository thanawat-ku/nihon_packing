<?php

namespace App\Action\Web;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SplitLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;

    public function __construct(
        Twig $twig,
        SplitLabelFinder $finder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-7 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }

        $viewData = [
            'splitLabels' => $this->finder->findSplitLabels($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];


        return $this->twig->render($response, 'web/splitLabels.twig', $viewData);
    }
}
