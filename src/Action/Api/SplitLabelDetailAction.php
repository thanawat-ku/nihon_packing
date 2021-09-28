<?php

namespace App\Action\Api;

use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
/**
 * Action.
 */
final class SplitLabelDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(SplitLabelDetailFinder $finder,Responder $responder)
    {
        $this->finder=$finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get SplitLabelDetail Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findSplitLabelDetails($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
