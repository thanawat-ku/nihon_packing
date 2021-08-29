<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
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
    private $finder;
    private $updater;

    public function __construct(SplitLabelFinder $finder,SplitLabelUpdater $updater,
Responder $responder)
    {
        $this->finder=$finder;
        $this->responder = $responder;
        $this->updater=$updater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message']="Get SplitLabel Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findSplitLabels($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
