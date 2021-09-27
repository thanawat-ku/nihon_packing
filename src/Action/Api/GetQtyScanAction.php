<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

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

    public function __construct(Twig $twig,LabelFinder $finder,ProductFinder $productFinder, 
    Session $session,Responder $responder, MergePackFinder $findermergepack)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->findermergepack=$findermergepack;
        // $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->session=$session;
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

                    $std_pack = $std_pack = $mergepack[$i]['std_pack'];
                    // $merge_no = $merge_pack_id[$i];

                    // if($merge_pack_id) {
                    //     $merge_no = $merge_pack_id[$i];
                    // }
            
                    // if ($merge_no) {
                    //     $rtdata['message'] = 'Login successfully';
                    //     $rtdata['error']=false;
                    //     $rtdata['merge_packs']=$merge_no ;
                    // } else {
                    //     $rtdata['message'] = 'Login fail';
                    //     $rtdata['error']=true;
                    //     $rtdata['merge_packs']=null;
                    // }


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