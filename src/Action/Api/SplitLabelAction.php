<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
   

    public function __construct(SplitLabelFinder $finder,Responder $responder)
    {
        $this->finder=$finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message']="Get SplitLabel Successful";
        $rtdata['error']=false;
        $rtdata['split_labels']=$this->finder->findSplitLabels($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
