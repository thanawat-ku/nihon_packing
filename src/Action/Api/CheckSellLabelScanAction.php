<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CheckSellLabelScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updateLabel;
    // private $upmergepack;
    // private $upmergepackdetail;
    // private $mergepackDetailFinder;
    private $updatesell;
    private  $updateselllabel;
    private  $findSellLabel;

    public function __construct(

        LabelFinder $finder,
        SellUpdater $updatesell,
        // LabelUpdater $updater,
        Responder $responder,
        SellLabelUpdater $updateselllabel,
        SellLabelFinder $findSellLabel,
        LabelUpdater $updateLabel
    ) {
        $this->finder = $finder;
        // $this->updater = $updater;
        $this->updatesell = $updatesell;
        $this->updateselllabel = $updateselllabel;
        $this->responder = $responder;
        $this->findSellLabel = $findSellLabel;
        $this->updateLabel = $updateLabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];
        $labels = $data['label'];
        $sellID = $data['sell_id'];

        $upstatus['up_status'] = "SELECTING_LABEL";
        $this->updatesell->updateSellStatus($sellID, $upstatus, $user_id);
        $this->updateselllabel->deleteSellLabelApi($sellID);

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
                    $arrSellLabel = $this->findSellLabel->checkLabelInSellLabel($data1['label_no']);
                    $rtdata['message'] = "Get SellLabel Successful";
                    $rtdata['error'] = true;
                    $rtdata['label_no'] = $arrSellLabel['label_no'];

                    return $this->responder->withJson($response, $rtdata);
                }
            }
            $labelRow = $this->finder->findLabelSingleTable($data1);

            if ($labelRow) {

                $insertSellLabel['label_id'] = $labelRow[0]['id'];
                $insertSellLabel['sell_id'] = $sellID;
                $this->updateselllabel->insertSellLabelApi($insertSellLabel, $user_id);

                if ($labelRow[0]['lot_id'] != 0) {
                    if ($labelRow[0]['status'] == "PACKED") {
                        $upStatus['status'] = "SELLING";
                        $this->updateLabel->updateLabelApi((int)$labelRow[0]['id'], $upStatus, $user_id);
                    }
                    $rtdata['message'] = "Get SellLabel Successful";
                    $rtdata['error'] = false;
                    $rtLabel = $this->findSellLabel->findSellLabelForlots($insertSellLabel);

                    array_push($listLabelFronLot, $rtLabel[0]);
                } else {
                    if ($labelRow[0]['status'] == "PACKED") {
                        $upStatus['status'] = "SELLING";
                        $this->updateLabel->updateLabelApi((int)$labelRow[0]['id'], $upStatus, $user_id);
                    }
                    $rtdata['message'] = "Get SellLabel Successful";
                    $rtdata['error'] = false;
                    $rtLabel = $this->findSellLabel->findSellLabelForMergePacks($insertSellLabel);

                    array_push($listLabelFronMerge, $rtLabel[0]);
                }
            } else {
                $rtdata['message'] = "Get SellLabel Successful";
                $rtdata['error'] = true;
                break;
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
        return $this->responder->withJson($response, $rtdata);
    }
}
