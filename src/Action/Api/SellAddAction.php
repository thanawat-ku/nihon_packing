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

        $selldata=$this->finder->findSells($params);
        $countsell = count($selldata);
        $countID = $countsell -1;

        if($countsell == 0){
            $params["sell_no"]="S00000000001";
        }else{
            $sell_id = $selldata[$countID]['id'];
            $sell_id += 1;
            $params["sell_no"]="S".str_pad( $sell_id, 11, "0", STR_PAD_LEFT);
        }

        $params["sell_date"]=Chronos::now()->toDateTimeString();

        $rtdata=$this->findproduct->findProducts($params);

        $params["product_id"]=$rtdata[0]['id'];
        $params["total_qty"]=0;
        $params["sell_status"]="CREATED";
        


        $this->updater->insertSellApi($params, $user_id);

        $rtdata['message']="Get Lot Defect Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->finder->findSells($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
