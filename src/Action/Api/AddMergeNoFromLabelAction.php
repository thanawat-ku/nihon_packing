<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class AddMergeNoFromLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $updatermergepackdetail;
    private $finLabel;
    private $updaterlabel;
    private $productFinder;

    public function __construct(MergePackFinder $finder,ProductFinder $productFinder, MergePackUpdater $updater,
    Responder $responder,  MergePackDetailUpdater $updatermergepackdetail,LabelFinder $finLabel, LabelUpdater $updaterlabel)
    {
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
        $this->updatermergepackdetail=$updatermergepackdetail;
        $this->finLabel = $finLabel;
        $this->updaterlabel=$updaterlabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id=$data["user_id"];
        $label_no = $data["label_no"];

        $params["label_no"]=$label_no;

        $rtdata['labels']=$this->finLabel->findCreateMergeNoFromLabels($params);
        $mpdata['merge_packs']=$this->finder->findMergePacks($params);

        $countlabel=count($rtdata["labels"]);
        $count_mp=count($mpdata["merge_packs"]);
        $count_mp -= 1;

        for($i=0; $i < $countlabel; $i++){
            if($rtdata["labels"][$i]['label_no']  == $label_no && $rtdata["labels"][$i]['label_type'] == "NONFULLY"){
                $productID = $rtdata["labels"][$i]['product_id'];
                $labelID = $rtdata["labels"][$i]['id'];
                $merpack_id = $mpdata["merge_packs"][$count_mp]['id'];
                $merpack_id += 1;

                $data1['merge_no']= "MP".str_pad($merpack_id, 10, "0", STR_PAD_LEFT); //สร้าง merge_no
                $data1['product_id']= $productID;
                $data1['merge_status']= "CREATED";

                $this->updater->insertMergePackApi($data1, $user_id);

                $data_mpd['merge_pack_id'] = $merpack_id;
                $data_mpd['label_id'] = $rtdata["labels"][$i]['id'];

                $this->updatermergepackdetail->insertMergePackDetailApi($data_mpd, $user_id);

                $data2['merge_pack_id'] = $merpack_id;
                $data2['status'] = "MERGING";

                $this->updaterlabel->updateLabelApi($labelID, $data2, $user_id);
                break;
            }
        }
        return $this->responder->withJson($response, $rtdata);
    }
}