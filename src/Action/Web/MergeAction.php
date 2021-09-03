<?php

namespace App\Action\Web;

use App\Domain\Merge\Service\MergeFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $mergeFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig,MergeFinder $mergeFinder,Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->mergeFinder=$mergeFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $viewData = [
            'labels' => $this->mergeFinder->findMerges($params), //Focus that!!!!!!
            'user_login' => $this->session->get('user'),
        ];
        

        return $this->twig->render($response, 'web/merges.twig',$viewData); //-----edit twig
    }
}
