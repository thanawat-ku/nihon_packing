<?php

namespace App\Action\Api;

use App\Domain\LabelPackMerge\Service\LabelPackMergeFinder;
use App\Domain\LabelPackMerge\Service\LabelPackMergeUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CancelAllLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelPackMergeFinder $finder,ProductFinder $productFinder, LabelPackMergeUpdater $updater,
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
        // $merge_status=$params["merge_status"];
        $user_id=$data["user_id"];
        $id=$data["merge_pack_id"];
        // $this->updater->updateLabelPackMergeApi($id, $params, $user_id);

        // $rtdata['message']="Get MergePack Successful";
        // $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabelPackMerges($data);
        $countlabel=count($rtdata["labels"]);
        for ($i=0; $i < $countlabel; $i++) {
            if ($rtdata["labels"][$i]['merge_pack_id'] == $id) {
                $data1['merge_pack_id'] = 0;
                $data1['status'] = "PACKED";
                $label_no = $rtdata["labels"][$i]['label_no'];
                $label_id = $rtdata["labels"][$i]['id'];


                $this->updater->updateLabelPackMergeApi($label_no, $data1, $user_id);

                $this->updater->deleteLabelMergePackApi($label_id, $data);
            }            
        }
        return $this->responder->withJson($response, $rtdata);
    }
}