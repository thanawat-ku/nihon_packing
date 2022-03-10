<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ConfirmSellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findSellCpoItem;
    private $updater;
    private $updatelabel;
    private $findSell;
    private $updateTag;
    private $findPacking;
    private $updatePacking;
    private $updatePackingItem;
    private $updateTempQuery;

    public function __construct(
        SellLabelFinder $finder,
        SellCpoItemFinder $findSellCpoItem,
        LabelUpdater $updatelabel,
        LabelFinder $findLabel,
        SellFinder $findSell,
        TagUpdater $updateTag,
        PackingFinder $findPacking,
        PackingUpdater $updatePacking,
        PackingItemUpdater $updatePackingItem,
        Session $session,
        Responder $responder,
        SellUpdater $updater,
        TempQueryUpdater $updateTempQuery,
    ) {
        $this->finder = $finder;
        $this->findSellCpoItem = $findSellCpoItem;
        $this->findLabel = $findLabel;
        $this->updatelabel = $updatelabel;
        $this->findSell = $findSell;
        $this->updateTag = $updateTag;
        $this->findPacking = $findPacking;
        $this->updatePacking = $updatePacking;
        $this->updatePackingItem = $updatePackingItem;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->updateTempQuery = $updateTempQuery;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['user_id'];
        $sellID = (int)$data['sell_id'];

        $dt = date('Y-m-d');
        $time  = strtotime($dt);
        $month = date('m', $time);
        $year  = date('Y', $time);
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $params['startDate'] = date('Y-' . $month . '-01');
        $params['endDate'] = date('Y-' . $month . '-' . $day);

        $rtPacking = $this->findPacking->findPacking($params);

        if (!$rtPacking) {
            $year2Di  = date('y');
            $packingNum = 1;
            $dateTime = date('Y-m-d h:i:s');
            $isPacking['PackingNo'] = 'PAK-' . $year2Di . $month . '-' . str_pad($packingNum, 4, "0", STR_PAD_LEFT);
            $isPacking['PackingNum'] = $packingNum;
            $isPacking['IssueDate'] = $dt;
            $isPacking['UpdateTime'] = $dateTime;
            $packingID = $this->updatePacking->insertPackingApi($isPacking, $user_id);

            //group lotID
            $data['find_lot_id'] = true;
            $rtSellLabel = $this->finder->findSellLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['sell_id'] = $data['sell_id'];
            $rtSellLabelPrefer = $this->finder->findSellLabels($dataPrefer);

            if ($rtSellLabel[0]['lot_id'] != 0) {
                for ($i = 0; $i < count($rtSellLabel); $i++) {
                    $labelFinder['lot_id'] = $rtSellLabel[$i]['lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->findSellCpoItem->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabel[$i]['lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;


                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmSellApi($sellID, $updateSell, $user_id);
                }
            }
            if ($rtSellLabelPrefer[0]['prefer_lot_id']) {
                for ($i = 0; $i < count($rtSellLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $data[$i]['prefer_lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->findSellCpoItem->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabel[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmSellApi($sellID, $updateSell, $user_id);
                }
            }
        } else {
            $year2Di  = date('y');
            $packingNum = $rtPacking[0]['PackingNum'] + 1;
            $dateTime = date('Y-m-d h:i:s');
            $isPacking['PackingNo'] = 'PAK-' . $year2Di . $month . '-' . str_pad($packingNum, 4, "0", STR_PAD_LEFT);
            $isPacking['PackingNum'] = $packingNum;
            $isPacking['IssueDate'] = $dt;
            $isPacking['UpdateTime'] = $dateTime;
            $packingID = $this->updatePacking->insertPackingApi($isPacking,  $user_id);

            //group lotID
            $data['find_lot_id'] = true;
            $rtSellLabel = $this->finder->findSellLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['sell_id'] = $data['sell_id'];
            $rtSellLabelPrefer = $this->finder->findSellLabels($dataPrefer);

            if ($rtSellLabel[0]['lot_id'] != 0) {
                for ($i = 0; $i < count($rtSellLabel); $i++) {
                    $labelFinder['lot_id'] = $rtSellLabel[$i]['lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->findSellCpoItem->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabel[$i]['lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmSellApi($sellID, $updateSell, $user_id);
                }
            }
            if ($rtSellLabelPrefer[0]['prefer_lot_id'] != 0) {
                for ($i = 0; $i < count($rtSellLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $data[$i]['prefer_lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->findSellCpoItem->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabel[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmSellApi($sellID, $updateSell, $user_id);
                }
            }
        }

        $sellLabel = $this->finder->findSellLabels($data);


        for ($i = 0; $i < count($sellLabel); $i++) {
            $labelID = (int)$sellLabel[$i]['label_id'];
            $dataUpdate['up_status'] = "USED";
            $this->updatelabel->updateLabelStatus($labelID, $dataUpdate, $user_id);
        }
        $updata['up_status'] = "PRINTED";
        $this->updater->updateSellStatus($sellID, $updata, $user_id);
        $allData = [''];

        $rtSell = $this->findSell->findSells($data);

        $rtSell['printer_id'] = $data['printer_id'];
        $rtTag = $this->updateTag->genTagsApi($sellID, $rtSell, $user_id);

        $this->updateTempQuery->deleteTempQuery((int)$rtSell[0]['product_id']);

        if (isset($data['start_date'])) {
            $allData['startDate'] = $data['start_date'];
            $allData['endDate'] = $data['end_date'];
        }

        $rtdata['message'] = "Get Sell Successful";
        $rtdata['error'] = false;
        $rtdata['sells'] = $this->findSell->findSells($allData);

        return $this->responder->withJson($response, $rtdata);
    }
}
