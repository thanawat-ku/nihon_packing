<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;

    public function __construct(Twig $twig,LotFinder $finder,Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $viewData = [
            'lots' => $this->finder->findLots($params),
            'user_login' => $this->session->get('user'),
        ];
        

        return $this->twig->render($response, 'web/lots.twig',$viewData);
    }
}
