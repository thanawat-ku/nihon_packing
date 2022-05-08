<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\StockControl\Service\StockControlUpdater;
use App\Domain\StockItem\Service\StockItemUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ConfirmPackLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findPackCpoItem;
    private $updater;
    private $updatelabel;
    private $findPack;
    private $updateTag;
    private $findPacking;
    private $updatePacking;
    private $updatePackingItem;
    private $updateTempQuery;
    private $stockControlUpdater;
    private $stockItemUpdater;
    private $lotFinder;
    private $lotUpdater;
    private $cpoItemFinder;
    private $cpoItemUpdater;
    private $packingItemFinder;

    public function __construct(
        PackLabelFinder $finder,
        PackCpoItemFinder $findPackCpoItem,
        LabelUpdater $updatelabel,
        LabelFinder $findLabel,
        PackFinder $findPack,
        TagUpdater $updateTag,
        PackingFinder $findPacking,
        PackingUpdater $updatePacking,
        PackingItemUpdater $updatePackingItem,
        Session $session,
        Responder $responder,
        PackUpdater $updater,
        TempQueryUpdater $updateTempQuery,
        StockControlUpdater $stockControlUpdater,
        StockItemUpdater $stockItemUpdater,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater,
        CpoItemFinder $cpoItemFinder,
        CpoItemUpdater $cpoItemUpdater,
        PackingFinder $packingItemFinder
    ) {
        $this->finder = $finder;
        $this->findPackCpoItem = $findPackCpoItem;
        $this->findLabel = $findLabel;
        $this->updatelabel = $updatelabel;
        $this->findPack = $findPack;
        $this->updateTag = $updateTag;
        $this->findPacking = $findPacking;
        $this->updatePacking = $updatePacking;
        $this->updatePackingItem = $updatePackingItem;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->updateTempQuery = $updateTempQuery;
        $this->stockControlUpdater = $stockControlUpdater;
        $this->stockItemUpdater = $stockItemUpdater;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
        $this->cpoItemFinder = $cpoItemFinder;
        $this->cpoItemUpdater = $cpoItemUpdater;
        
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['user_id'];
        $packID = (int)$data['pack_id'];

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
            $isPacking['PackingNo'] = 'PAK-' . $year2Di . $month . '-' . str_pad($packingNum, strlen($packingNum), "", STR_PAD_LEFT);
            $isPacking['PackingNum'] = $packingNum;
            $isPacking['IssueDate'] = $dt;
            $isPacking['UpdateTime'] = $dateTime;
            $packingID = $this->updatePacking->insertPackingApi($isPacking, $user_id);

            //group lotID
            $data['find_lot_id'] = true;
            $rtPackLabel = $this->finder->findPackLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['pack_id'] = $data['pack_id'];
            $rtPackLabelPrefer = $this->finder->findPackLabels($dataPrefer);

            if (isset($rtPackLabel[0]['lot_id']) != 0 && $rtPackLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtPackLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtPackLabel); $i++) {
                    $labelFinder['lot_id'] = $rtPackLabel[$i]['lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->findPackCpoItem->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtPackLabel[$i]['lot_id'];

                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQty += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;


                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmPackApi($packID, $updatePack, $user_id);
                }
            }
            if (isset($rtPackLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtPackLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->findPackCpoItem->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQty += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmPackApi($packID, $updatePack, $user_id);
                }
            }
            //update stockcontrol, stockitem and lot

            $paramsPackingID['PackingID'] = $packingID;
            $arrPacking = $this->findPacking->findPackingItem($paramsPackingID);

            $insertStockContol['PackingNo'] = $arrPacking[0]['PackingNo'];
            $stockControlID = $this->stockControlUpdater->insertStockControlApi($insertStockContol, $user_id);

            for ($i = 0; $i < count($arrPacking); $i++) {
                $insertStockItem['StockControlID'] = $stockControlID;
                $insertStockItem['LotID'] = $arrPacking[$i]['LotID'];
                $insertStockItem['Quantity'] = $arrPacking[$i]['Quantity'];

                $this->stockItemUpdater->insertStockItem($insertStockItem);

                $updateStockItem['OutStockControlID'] = $stockControlID;
                $lotID = $arrPacking[$i]['LotID'];
                $this->stockItemUpdater->updateStockItem($lotID, $updateStockItem);

                $searchLotID['LotID'] = $lotID;
                $rowLot = $this->lotFinder->findLotNsps($searchLotID);
                $packingQtyOld = $rowLot[0]['PackingQty'];
                $packingQtyNew = $packingQtyOld + $arrPacking[$i]['Quantity'];
                $updaeteLot['PackingQty'] = $packingQtyNew;
                $this->lotUpdater->updateLotNsp($lotID, $updaeteLot);
            }

            //#####################################################################
        } else {
            $year2Di  = date('y');
            $packingNum = $rtPacking[0]['PackingNum'] + 1;
            $dateTime = date('Y-m-d h:i:s');
            $isPacking['PackingNo'] = 'PAK-' . $year2Di . $month . '-' . str_pad($packingNum, strlen($packingNum), "", STR_PAD_LEFT);
            $isPacking['PackingNum'] = $packingNum;
            $isPacking['IssueDate'] = $dt;
            $isPacking['UpdateTime'] = $dateTime;
            $packingID = $this->updatePacking->insertPackingApi($isPacking,  $user_id);

            //group lotID
            $data['find_lot_id'] = true;
            $rtPackLabel = $this->finder->findPackLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['pack_id'] = $data['pack_id'];
            $rtPackLabelPrefer = $this->finder->findPackLabels($dataPrefer);

            if (isset($rtPackLabel[0]['lot_id']) != 0 && $rtPackLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtPackLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtPackLabel); $i++) {
                    $labelFinder['lot_id'] = $rtPackLabel[$i]['lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->findPackCpoItem->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtPackLabel[$i]['lot_id'];

                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQty += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmPackApi($packID, $updatePack, $user_id);
                }
            }
            if (isset($rtPackLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtPackLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->findPackCpoItem->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQty += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->updatePackingItem->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateConfirmPackApi($packID, $updatePack, $user_id);
                }
            }

            //update stockcontrol, stockitem and lot

            $paramsPackingID['PackingID'] = $packingID;
            $arrPacking = $this->findPacking->findPackingItem($paramsPackingID);

            $insertStockContol['PackingNo'] = $arrPacking[0]['PackingNo'];
            $stockControlID = $this->stockControlUpdater->insertStockControlApi($insertStockContol, $user_id);

            for ($i = 0; $i < count($arrPacking); $i++) {
                $insertStockItem['StockControlID'] = $stockControlID;
                $insertStockItem['LotID'] = $arrPacking[$i]['LotID'];
                $insertStockItem['Quantity'] = $arrPacking[$i]['Quantity'];

                $this->stockItemUpdater->insertStockItem($insertStockItem);

                $updateStockItem['OutStockControlID'] = $stockControlID;
                $lotID = $arrPacking[$i]['LotID'];
                $this->stockItemUpdater->updateStockItem($lotID, $updateStockItem);

                $searchLotID['LotID'] = $lotID;
                $rowLot = $this->lotFinder->findLotNsps($searchLotID);
                $packingQtyOld = $rowLot[0]['PackingQty'];
                $packingQtyNew = $packingQtyOld + $arrPacking[$i]['Quantity'];
                $updaeteLot['PackingQty'] = $packingQtyNew;
                $this->lotUpdater->updateLotNsp($lotID, $updaeteLot);
            }

            //#####################################################################
        }

        //update cpo item

        $rtPacking['PackingID'] = $packingID;

        $sumQuantity = 0;
        for ($i = 0; $i < count($arrPacking); $i++) {
            $sumQuantity += $arrPacking[$i]['Quantity'];
        }

        $rtSearch['cpo_item_id'] = $arrPacking[0]['CpoItemID'];
        $rtCpoItem = $this->cpoItemFinder->findCpoItem($rtSearch);
        $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty'];

        $this->cpoItemUpdater->updateCpoItem((int)$arrPacking[0]['CpoItemID'], $isCpoItem);

        //#####################################################################

        $dataPackID['pack_id'] = $data['pack_id'];
        $packLabel = $this->finder->findPackLabels($dataPackID);

        for ($i = 0; $i < count($packLabel); $i++) {
            $labelID = (int)$packLabel[$i]['label_id'];
            $dataUpdate['up_status'] = "USED";
            $this->updatelabel->updateLabelStatus($labelID, $dataUpdate, $user_id);
        }
        $updata['up_status'] = "PRINTED";
        $this->updater->updatePackStatus($packID, $updata, $user_id);
        $allData = [''];

        $rtPack = $this->findPack->findPacks($data);

        $rtPack['printer_id'] = $data['printer_id'];
        $rtTag = $this->updateTag->genTagsApi($packID, $rtPack, $user_id);

        $this->updateTempQuery->deleteTempQuery((int)$rtPack[0]['product_id']);

        if (isset($data['start_date'])) {
            $allData['startDate'] = $data['start_date'];
            $allData['endDate'] = $data['end_date'];
        }

        $rtdata['message'] = "Get Pack Successful";
        $rtdata['error'] = false;
        $rtdata['packs'] = $this->findPack->findPacks($allData);

        return $this->responder->withJson($response, $rtdata);
    }
}
