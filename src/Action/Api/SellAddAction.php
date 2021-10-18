<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class SellAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findproduct;
    private $findcpo_item;


    public function __construct(Responder $responder,  SellUpdater $updater, SellFinder $finder, ProductFinder $findproduct, CpoItemFinder $findcpo_item)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder=$finder;
        $this->findproduct=$findproduct;
        $this->findcpo_item=$findcpo_item;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
       
        $params = (array)$request->getParsedBody();

        $user_id=$params['user_id'];

        $id=$this->updater->insertSellApi($params, $user_id);
        $params['id']=$id;
        
        // $rtdata=$this->findproduct->findProducts($params);

        $rtdata['message']="Get Lot Defect Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->finder->findSells($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
