<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PrintLabelMergePackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $productFinder;

    public function __construct(LabelFinder $finder,ProductFinder $productFinder, LabelUpdater $updater,
    Responder $responder)
    {
        
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
       
        $data = (array)$request->getParsedBody();
        $user_id=$data["user_id"];
        $mergepackID=$data["merge_pack_id"];

        $params["merge_pack_id"]=$mergepackID;

        $rtdata['labels']=$this->finder->findLabels($params);

        $countlabel=count($rtdata["labels"]);
        $sumqty=0;

        for($i=0; $i < $countlabel; $i++){
            if($rtdata["labels"][$i]['merge_pack_id']  == $mergepackID && $rtdata["labels"][$i]['status'] == "MERGING"){
               $quantity = $rtdata["labels"][$i]['quantity'];
               $std_pack = $rtdata["labels"][$i]['std_pack'];
               $product_id = $rtdata["labels"][$i]['part_code'];
               $label_id = $rtdata["labels"][$i]['id'];
               $sumqty=$quantity+$sumqty;

               $this->updater->updateLabelMergePackApi($label_id, $data, $user_id);
               
            }
        }
        $num_packs=floor($sumqty / $std_pack);
        $mod_num_pack = $sumqty % $std_pack;
        $counti = 0;
        for($i=0; $i <  $num_packs; $i++){
            $data1['lot_id']= 0;
            $data1['merge_pack_id']= $mergepackID;
            $data1['split_label_id']= 0;
            $data1['label_no']=$mergepackID.$product_id.str_pad( $i, 5, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_FULLY";
            $data1['quantity']=$std_pack;
            $data1['status']="CREATED";

            $counti=$counti+1;

            $this->updater->insertLabelMergePackApi($data1, $user_id);
        }
        if($mod_num_pack != 0){
            $data1['lot_id']= 0;
            $data1['merge_pack_id']= $mergepackID;
            $data1['split_label_id']= 0;
            $data1['label_no']=$mergepackID.$product_id.str_pad( $counti, 5, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_NONFULLY";
            $data1['quantity']=$mod_num_pack;
            $data1['status']="CREATED";
    
            $this->updater->insertLabelMergePackApi($data1, $user_id);
        }
       

        return $this->responder->withJson($response,  $rtdata); 

    }
}