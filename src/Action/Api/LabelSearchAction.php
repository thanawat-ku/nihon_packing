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
final class LabelSearchAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $productFinder;

    public function __construct(LabelFinder $finder,ProductFinder $productFinder, 
    Responder $responder)
    {
        
        $this->finder=$finder;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
       
        $data = (array)$request->getParsedBody();

        $labelNO = $data['label_no'];

        $labelRow = $this->finder->checklabel($labelNO);

        if($labelRow) {
            $label = $labelRow;

            if($label["status"] == "MERGING" || $label["status"] == "MERGED"){
                $array_label = array("error");
    
                $rtdata['error']=true;
                $rtdata['labels'] = $array_label;
            }
            else{
                $rtdata['error'] = false;
                $rtdata['labels'] = $label;
                    
            }
        }
        else{
            $array_label = array("error");
            $rtdata['error']=true;
            $rtdata['labels'] = $array_label;
        }

        return $this->responder->withJson($response, $rtdata);

    }
}