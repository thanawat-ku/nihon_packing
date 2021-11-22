<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelScanMergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $upmergepack;
    private $upmergepackdetail;
    private $mergepackDetailFinder;

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        MergePackUpdater $upmergepack,
        LabelUpdater $updater,
        Session $session,
        Responder $responder,
        MergePackDetailUpdater $upmergepackdetail,
        MergePackDetailFinder $mergepackDetailFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->upmergepack = $upmergepack;
        $this->upmergepackdetail = $upmergepackdetail;
        $this->session = $session;
        $this->responder = $responder;
        $this->mergepackDetailFinder = $mergepackDetailFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];

        $labelRow = $this->finder->findLabelSingleTable($data);

        if (($labelRow[0]['label_type'] == "NONFULLY" || $labelRow[0]['label_type'] == "MERGE_NONFULLY") && $labelRow[0]['status'] == "CREATED") {
            $lotID = $labelRow[0]['lot_id'];
            $mergePackID = $labelRow[0]['merge_pack_id'];

            
           

            if ($lotID != 0) {
                $dataLotAndMerge['lot_id'] = $lotID;

                $rtLabelFromLot = $this->finder->findCreateMergeNoFromLabels($dataLotAndMerge);

                $productID = $rtLabelFromLot[0]['product_id'];
                $rtLabel['id'] = $labelRow[0]['id'];
                $rtLabel['product_id'] = $productID;
                $id = $this->upmergepack->insertMergePackApi($rtLabel, $user_id);
                $this->upmergepack->updateMergingApi($id, $rtLabel, $user_id);
                $rtLabel['check_mp_id'] = $id;

                $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($rtLabel, $user_id);
                $rtLabel['status'] = "MERGING";
                $this->updater->updateLabelStatusMerging($id, $rtLabel, $user_id);


                $dataMergeDetail['merge_pack_id'] = $id;
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = false;
                $rtdata['mpd_from_lots'] = $this->mergepackDetailFinder->findMergePackDetailFromLots($dataMergeDetail);
            } elseif ($mergePackID != 0) {
                $dataLotAndMerge['merge_pack_id'] = $mergePackID;

                $rtLabelFromMerge = $this->finder->findLabelCreateFromMerges($dataLotAndMerge);

                $productID = $rtLabelFromMerge[0]['product_id'];
                $rtLabel['id'] = $labelRow[0]['id'];
                $rtLabel['check_product_id'] = $productID;

                $id = $this->upmergepack->insertMergePackApi($rtLabel, $user_id);
                $this->upmergepack->updateMergingApi($id, $rtLabel, $user_id);
                $rtLabel['check_mp_id'] = $id;

                $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($rtLabel, $user_id);
                $this->updater->updateLabelStatusMerging($id, $rtLabel, $user_id);

                
                $dataMergeDetail['merge_pack_id'] = $id;
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = false;
                $rtdata['mpd_from_merges'] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($dataMergeDetail);
            }
        } else {
            $rtdata['message'] = "Get Label Successful";
            $rtdata['error'] = true;
        }

        // $this->upmergepack->updateMergingApi($mergePackID, $data, $user_id);
        // $this->upmergepackdetail->deleteMergePackDetailApi($mergePackID);

        // $arrlabel = explode("#", $label);

        // for ($i = 1; $i < count($arrlabel); $i++) {
        //     $label_no = $arrlabel[$i];
        //     $data1['label_no'] = explode(",", $label_no)[0];
        //     $labelRow = $this->finder->findLabelSingleTable($data1);

        //     if ($labelRow) {
        //         if ($labelRow[0]['mergo_pack_id'] == 0) {
        //             $labelRow['check_mp_id'] = $mergePackID;
        //             $labelRow['check_product_id'] = $productID;
        //             $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($labelRow, $user_id);
        //             $this->updater->updateLabelStatusMerging($id, $labelRow, $user_id);

        //             if ($labelRow[0]['lot_id'] != 0) {
        //                 $data["merge_pack_id"] = $mergePackID;
        //                 $rtdata['message'] = "Get Label Successful";
        //                 $rtdata['error'] = false;
        //                 $rtdata['mpd_from_lots'] = $this->mergepackDetailFinder->findMergePackDetailFromLots($data);
        //             } else {
        //                 $data["merge_pack_id"] = $mergePackID;
        //                 $rtdata['message'] = "Get Label Successful";
        //                 $rtdata['error'] = false;
        //                 $rtdata['mpd_from_merges'] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($data);
        //             }
        //         } else {
        //             $rtdata['message'] = "Get Label Successful";
        //             $rtdata['error'] = true;
        //         }
        //     } else {
        //     }
        // }

        return $this->responder->withJson($response, $rtdata);
    }
}
