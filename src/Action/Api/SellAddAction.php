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

        $user_id=$params["user_id"];
        $product_name=$params["product_name"];

        $rtdata=$this->findproduct->findIDFromProductName($product_name);

        $params["product_id"]=$rtdata['id'];
        
        $cpodata=$this->findcpo_item->findIDFromProductName($params, $params["product_id"]);

        $selldata=$this->finder->findSells($params);
        $countsell = count($selldata);
        $countID = $countsell -1;
        // $ID= (string)$countID;

        $total_qty = 0;


        for ($i=0; $i < count($cpodata); $i++) { 
            // $cpodata=$this->findcpo_item->findCpoItem($params);
            if ($cpodata[$i]['Quantity'] > $cpodata[$i]['PackingQty']) {
                $total_qty = $total_qty +  $cpodata[$i]['PackingQty'];
            }

        }
        

        if($countsell == 0){
            $params["sell_no"]="S00000000000";
        }else{
            $sell_id = $selldata[$countID]['id'];
            $sell_id += 1;
            $params["sell_no"]="S".str_pad( $sell_id, 11, "0", STR_PAD_LEFT);
        }

        // for ($i=0; $i < ; $i++) { 
        //     # code...
        // }


        // $params["sell_date"]="S000000000001";
        $params["total_qty"]=$total_qty;
        $params["sell_status"]="CREATED";
        $params["sell_date"]=Chronos::now()->toDateTimeString();


        $this->updater->insertSellApi($params, $user_id);

        $rtdata['message']="Get Lot Defect Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->finder->findSells($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
