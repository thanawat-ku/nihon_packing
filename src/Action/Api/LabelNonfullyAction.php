<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class LabelNonfullyAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(LabelFinder $finder,ProductFinder $productFinder,
    Responder $responder)
    {
        
        $this->finder=$finder;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
        
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get LabelNonfully Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabelNonfullys($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}