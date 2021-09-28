<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class GetQtyScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findermergepack;
    private $updater;

    public function __construct(LabelFinder $finder,ProductFinder $productFinder, 
    Responder $responder, MergePackFinder $findermergepack)
    {
       
        $this->finder=$finder;
        $this->findermergepack=$findermergepack;
        // $this->updater=$updater;
        $this->productFinder=$productFinder;
        
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $mrege_pack_id = (int)($data['merge_pack_id'] ?? '');
        $product_id = $data['product_id'];
        $user_id = $data['user_id'];

        $label = $this->finder->findLabelNonfullys($data);
        $mergepack = $this->findermergepack->findMergePacks($data);
        
        $countlb = count($label);
        $sum_qty = 0;
        $countmp = count($mergepack);
        $std_pack = 0;

        $merge_pack_id = $this->findermergepack->findMergePacks($data);

        if ($mrege_pack_id == 0) {

            $countmp = count($merge_pack_id);

            for($i=0; $i<$countmp;$i++){
                if ($merge_pack_id[$i]["product_id"] == $product_id && $merge_pack_id[$i]["merge_status"] == "CREATED" && $merge_pack_id[$i]["created_user_id"] == $user_id) {

                    $std_pack = $mergepack[$i]['std_pack'];
                    break;

                }

            }
        }else{

            for($i=0; $i<$countlb;$i++) {
                if($label[$i]['merge_pack_id'] == $mrege_pack_id && $label[$i]['status'] == "MERGING" && $label[$i]['label_type'] == "NONFULLY"){
                    $qty = $label[$i]['quantity'];
                    $sum_qty += $qty;   
                }
            }
            for($i=0; $i<$countmp;$i++) {
                if($mergepack[$i]['id'] == $mrege_pack_id){
                    $std_pack = $mergepack[$i]['std_pack'];
                    break;
                }
            }
    
           
        }
        $array_labels = array($sum_qty,  $std_pack);
    
        $rtdata['labels'] = $array_labels;
        
        return $this->responder->withJson($response, $rtdata);
    }
}