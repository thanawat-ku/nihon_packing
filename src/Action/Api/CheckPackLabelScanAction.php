<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class CheckPackLabelScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updateLabel;
    private $updatesell;
    private  $updateselllabel;
    private  $findPackLabel;

    public function __construct(

        LabelFinder $finder,
        PackUpdater $updatesell,
        Responder $responder,
        PackLabelUpdater $updateselllabel,
        PackLabelFinder $findPackLabel,
        LabelUpdater $updateLabel
    ) {
        $this->finder = $finder;
        $this->updatesell = $updatesell;
        $this->updateselllabel = $updateselllabel;
        $this->responder = $responder;
        $this->findPackLabel = $findPackLabel;
        $this->updateLabel = $updateLabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];
        $labels = $data['label'];
        $sellID = $data['pack_id'];

        $this->updateselllabel->deletePackLabelApi($sellID);

        $arrlabel = explode("#", $labels);

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
                    $arrPackLabel = $this->findPackLabel->checkLabelInPackLabel($data1['label_no']);
                    $rtdata['message'] = "Get PackLabel Successful";
                    $rtdata['error'] = true;
                    $rtdata['label_no'] = $arrPackLabel['label_no'];

                    return $this->responder->withJson($response, $rtdata);
                }
            }
            $labelRow = $this->finder->findLabelSingleTable($data1);

            if ($labelRow) {

                $insertPackLabel['label_id'] = $labelRow[0]['id'];
                $insertPackLabel['pack_id'] = $sellID;
                $this->updateselllabel->insertPackLabelApi($insertPackLabel, $user_id);

                if ($labelRow[0]['lot_id'] != 0) {
                    if ($labelRow[0]['status'] == "PACKED") {
                        $upStatus['status'] = "SELLING";
                        $this->updateLabel->updateLabelApi((int)$labelRow[0]['id'], $upStatus, $user_id);
                    }
                    $rtdata['message'] = "Get PackLabel Successful";
                    $rtdata['error'] = false;
                    $rtLabel = $this->findPackLabel->findPackLabelForlots($insertPackLabel);

                    array_push($listLabelFronLot, $rtLabel[0]);
                } else {
                    if ($labelRow[0]['status'] == "PACKED") {
                        $upStatus['status'] = "SELLING";
                        $this->updateLabel->updateLabelApi((int)$labelRow[0]['id'], $upStatus, $user_id);
                    }
                    $rtdata['message'] = "Get PackLabel Successful";
                    $rtdata['error'] = false;
                    $rtLabel = $this->findPackLabel->findPackLabelForMergePacks($insertPackLabel);

                    array_push($listLabelFronMerge, $rtLabel[0]);
                }
            } else {
                // $rtdata['message'] = "Get PackLabel Successful";
                // $rtdata['error'] = true;
                // break;
            }

            if ($listLabelFronLot != null && $listLabelFronMerge == null) {
                $rtdata['check_label_from_sl'] = "lot";
                $rtdata['mpd_from_lots'] = $listLabelFronLot;
            } else if ($listLabelFronMerge != null &&  $listLabelFronLot == null) {
                $rtdata['check_label_from_sl'] = "merge";
                $rtdata['mpd_from_merges'] = $listLabelFronMerge;
            } else if ($listLabelFronLot != null && $listLabelFronMerge  != null) {
                $rtdata['check_label_from_sl'] = "lot_and_merge";
                $rtdata['mpd_from_lots'] = $listLabelFronLot;
                $rtdata['mpd_from_merges'] = $listLabelFronMerge;
            }
        }

        $upstatus['up_status'] = "SELECTING_LABEL";
        $this->updatesell->updatePackStatus($sellID, $upstatus, $user_id);

        return $this->responder->withJson($response, $rtdata);
    }
}
