<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SplitLabelAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,SplitLabelFinder $finder,
    Session $session,Responder $responder)
    {
        $this->finder=$finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];

        $this->updater->insertSplitLabelApi($params,$user_id );
        
        $rtdata['message']="Get SplitLabelAdd Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findSplitLabels($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
