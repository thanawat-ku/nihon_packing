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
final class CheckLabelScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $upmergepack;
    private $upmergepackdetail;
    private $mergepackDetailFinder;
    private $session;

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
        $label = $data['label'];
        $mergePackID = $data['id'];
        $productID = $data['product_id'];

        $this->upmergepack->updateMergingApi($mergePackID, $data, $user_id);
        $this->upmergepackdetail->deleteMergePackDetailApi($mergePackID);

        $arrlabel = explode("#", $label);

        $listLabelFronLot = [];
        $listLabelFronMerge = [];
        $checkLabel = [];

        for ($i = 1; $i < count($arrlabel); $i++) {
            $labelNo = $arrlabel[$i];
            $data1['label_no'] = explode(",", $labelNo)[0];
            array_push($checkLabel, $data1['label_no']);

            $labelvalue = array_count_values($checkLabel);

            foreach ($labelvalue as $key => $value) {
                if ($value > 1) {
                    $arrMpDetail = $this->mergepackDetailFinder->checkLabelInMergePackDetail($data1['label_no']);
                    $rtdata['message'] = "Get PackLabel Successful";
                    $rtdata['error'] = true;
                    $rtdata['label_no'] = $arrMpDetail['label_no'];

                    return $this->responder->withJson($response, $rtdata);
                }
            }

            $labelRow = $this->finder->findLabelSingleTable($data1);

            if ($labelRow) {
                if ($labelRow[0]['merge_pack_id'] == 0) {
                    $insertMergeDetail['merge_pack_id'] = $mergePackID;
                    $insertMergeDetail['label_id'] = $labelRow[0]['id'];
                    $labelRow['check_product_id'] = $productID;
                    $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($insertMergeDetail, $user_id);
                    $this->updater->updateLabelStatusMerging($id, $labelRow, $user_id);

                    if ($labelRow[0]['lot_id'] != 0) {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtLabel[0] = $this->mergepackDetailFinder->findMergePackDetailFromLots($data);

                        array_push($listLabelFronLot, $rtLabel[0][0]);
                    } else {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtLabel[0] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($data);

                        array_push($listLabelFronMerge, $rtLabel[0][0]);
                    }
                } else {
                    // $rtdata['message'] = "Get PackLabel Successful";
                    // $rtdata['error'] = true;
                    // break;
                    $insertMergeDetail['merge_pack_id'] = $mergePackID;
                    $insertMergeDetail['label_id'] = $labelRow[0]['id'];
                    $labelRow['check_product_id'] = $productID;
                    $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($insertMergeDetail, $user_id);
                    $this->updater->updateLabelStatusMerging($id, $labelRow, $user_id);

                    if ($labelRow[0]['lot_id'] != 0) {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtLabel[0] = $this->mergepackDetailFinder->findMergePackDetailFromLots($data);

                        array_push($listLabelFronLot, $rtLabel[0][0]);
                    } else {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtLabel[0] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($data);

                        array_push($listLabelFronMerge, $rtLabel[0][0]);
                    }
                }

                if ($listLabelFronLot != null && $listLabelFronMerge == null) {
                    $rtdata['check_label_from_mpd'] = "lot";
                    $rtdata['mpd_from_lots'] = $listLabelFronLot;
                } else if ($listLabelFronMerge != null &&  $listLabelFronLot == null) {
                    $rtdata['check_label_from_mpd'] = "merge";
                    $rtdata['mpd_from_merges'] = $listLabelFronMerge;
                } else if ($listLabelFronLot != null && $listLabelFronMerge  != null) {
                    $rtdata['check_label_from_mpd'] = "lot_and_merge";
                    $rtdata['mpd_from_lots'] = $listLabelFronLot;
                    $rtdata['mpd_from_merges'] = $listLabelFronMerge;
                }
            }
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
