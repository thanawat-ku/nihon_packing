<?php

namespace App\Action\Api;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotDefectAddAction
{
    private $responder;
    private $updater;
    private $finder;


    public function __construct(Responder $responder,  LotDefectUpdater $updater, LotDefectFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder=$finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
       
        $data = (array)$request->getParsedBody();
        
        $this->updater->insertLotDefect( $data);

        $params = (array)$request->getQueryParams();

        $rtdata['message']="Add LotDefect Successful";
        $rtdata['error']=false;
        $rtdata['lot_defects']=$this->finder->findLotDefects($params);
 
        return $this->responder->withJson($response, $rtdata);
    }
}

