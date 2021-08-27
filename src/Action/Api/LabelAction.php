<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,LabelFinder $finder,
    Session $session,Responder $responder)
    {
        $this->finder=$finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get Label Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabels($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
