<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CheckMergePackIDAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,MergePackFinder $finder,ProductFinder $productFinder, MergePackUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        // $merge_status = (string)($data['merge_status'] ?? '');
        $product_id = (int)($data['product_id'] ?? '');
        $user_id = (int)($data['user_id'] ?? '');
        $merge_pack_ID = $data['id'];


        if($merge_pack_ID == 0){
            $merge_no = null;

            $merge_pack_id = $this->finder->findMergePacks($data);
            $countmp = count($merge_pack_id);

            for($i=0; $i<$countmp;$i++){
                if ($merge_pack_id[$i]["product_id"] == $product_id && $merge_pack_id[$i]["merge_status"] == "CREATED" && $merge_pack_id[$i]["created_user_id"] == $user_id) {
                    $merge_no = $merge_pack_id[$i];

                    if($merge_pack_id) {
                        $merge_no = $merge_pack_id[$i];
                    }
            
                    if ($merge_no) {
                        $rtdata['message'] = 'Login successfully';
                        $rtdata['error']=false;
                        $rtdata['merge_packs']=$merge_no ;
                    } else {
                        $rtdata['message'] = 'Login fail';
                        $rtdata['error']=true;
                        $rtdata['merge_packs']=null;
                    }
                }

            }
        }else{
            $merge_no = null;

            $merge_pack_id = $this->finder->findMergePacks($data);
            $countmp = count($merge_pack_id);

            for($i=0; $i<$countmp;$i++){
                if ($merge_pack_id[$i]["product_id"] == $product_id && $merge_pack_id[$i]["id"] == $merge_pack_ID) {
                    $merge_no = $merge_pack_id[$i];

                    if($merge_pack_id) {
                        $merge_no = $merge_pack_id[$i];
                    }
                    if ($merge_no) {
                        $rtdata['message'] = 'Login successfully';
                        $rtdata['error']=false;
                        $rtdata['merge_packs']=$merge_no ;
                    } else {
                        $rtdata['message'] = 'Login fail';
                        $rtdata['error']=true;
                        $rtdata['merge_packs']=null;
                    }
                }
            }
        } 
        return $this->responder->withJson($response, $rtdata);
    }
}