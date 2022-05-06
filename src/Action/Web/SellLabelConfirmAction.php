<?php

namespace App\Action\Web;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\StockControl\Service\StockControlUpdater;
use App\Domain\StockItem\Service\StockItemUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellLabelConfirmAction
{
    private $responder;
    private $finder;
    private $sellCpoItemFinder;
    private $updater;
    private $labelFinder;
    private $labelUpdater;
    private $session;
    private $sellFinder;
    private $updateTag;
    private $packingFinder;
    private $packingUpdate;
    private $packingItemUpdate;
    private $tempQueryUpdater;
    private $stockControlUpdater;
    private $stockItemUpdater;
    private $lotFinder;
    private $lotUpdater;

    public function __construct(
        Responder $responder,
        SellLabelFinder $finder,
        SellCpoItemFinder $sellCpoItemFinder,
        SellUpdater $updater,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        SellFinder $sellFinder,
        TagUpdater $updateTag,
        PackingFinder $packingFinder,
        PackingUpdater $packingUpdate,
        PackingItemUpdater $packingItemUpdate,
        TempQueryUpdater $tempQueryUpdater,
        StockControlUpdater $stockControlUpdater,
        StockItemUpdater $stockItemUpdater,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater,
        Session $session
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->sellCpoItemFinder = $sellCpoItemFinder;
        $this->updater = $updater;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
        $this->sellFinder = $sellFinder;
        $this->updateTag = $updateTag;
        $this->packingFinder = $packingFinder;
        $this->packingUpdate = $packingUpdate;
        $this->packingItemUpdate = $packingItemUpdate;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->stockControlUpdater = $stockControlUpdater;
        $this->stockItemUpdater = $stockItemUpdater;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        // //Test update stockcontrol, stockitem and lot
        
        // $paramsPackingID['PackingID'] = 493251;
        // $arrPacking = $this->packingFinder->findPackingItem($paramsPackingID);

        // $insertStockContol['PackingNo'] = $arrPacking[0]['PackingNo'];
        // $stockControlID = $this->stockControlUpdater->insertStockControl($insertStockContol);

        // for ($i = 0; $i < count($arrPacking); $i++) {
        //     $insertStockItem['StockControlID'] = $stockControlID;
        //     $insertStockItem['LotID'] = $arrPacking[$i]['LotID'];
        //     $insertStockItem['Quantity'] = $arrPacking[$i]['Quantity'];

        //     $this->stockItemUpdater->insertStockItem($insertStockItem);

        //     $updateStockItem['OutStockControlID'] = $stockControlID;
        //     $lotID = $arrPacking[$i]['LotID'];
        //     $this->stockItemUpdater->updateStockItem($lotID, $updateStockItem);

        //     $searchLotID['LotID'] = $lotID;
        //     $rowLot = $this->lotFinder->findLotNsps($searchLotID);
        //     $packingQtyOld = $rowLot[0]['PackingQty'];
        //     $packingQtyNew = $packingQtyOld + $arrPacking[$i]['Quantity'];
        //     $updaeteLot['PackingQty'] = $packingQtyNew;
        //     $this->lotUpdater->updateLotNsp($lotID, $updaeteLot);
        // }

        // //#####################################################################

        $sellID = (int)$data['sell_id'];

        $user_id = $this->session->get('user')["id"];

        $dt = date('Y-m-d');
        $time  = strtotime($dt);
        $month = date('m', $time);
        $year  = date('Y', $time);
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $params['startDate'] = date('Y-' . $month . '-01');
        $params['endDate'] = date('Y-' . $month . '-' . $day);

        //test date empty
        // $params['startDate'] = date('Y-' .'03' . '-01');
        // $params['endDate'] = date('Y-' . '03' . '-' . 30);

        $rtPacking = $this->packingFinder->findPacking($params);

        if (!$rtPacking) {
            $year2Di  = date('y');
            $packingNum = 1;
            $dateTime = date('Y-m-d h:i:s');
            $isPacking['PackingNo'] = 'PAK-' . $year2Di . $month . '-' . str_pad($packingNum, strlen($packingNum), "", STR_PAD_LEFT);
            $isPacking['PackingNum'] = $packingNum;
            $isPacking['IssueDate'] = $dt;
            $isPacking['UpdateTime'] = $dateTime;
            $packingID = $this->packingUpdate->insertPacking($isPacking);

            //group lotID
            $data['find_lot_id'] = true;
            $rtSellLabel = $this->finder->findSellLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['sell_id'] = $data['sell_id'];
            $rtSellLabelPrefer = $this->finder->findSellLabels($dataPrefer);

            if (isset($rtSellLabel[0]['lot_id']) != 0 && $rtSellLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtSellLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtSellLabel); $i++) {
                    $labelFinder['lot_id'] = $rtSellLabel[$i]['lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtSellLabel[$i]['lot_id'];

                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;


                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateSell($sellID, $updateSell);
                }
            }
            if (isset($rtSellLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtSellLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $rtSellLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateSell($sellID, $updateSell);
                }
            }

            //update stockcontrol, stockitem and lot

            $paramsPackingID['PackingID'] = $packingID;
            $arrPacking = $this->packingFinder->findPackingItem($paramsPackingID);

            $insertStockContol['PackingNo'] = $arrPacking[0]['PackingNo'];
            $stockControlID = $this->stockControlUpdater->insertStockControl($insertStockContol);

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
            $packingID = $this->packingUpdate->insertPacking($isPacking);

            //group lotID
            $data['find_lot_id'] = true;
            $rtSellLabel = $this->finder->findSellLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['sell_id'] = $data['sell_id'];
            $rtSellLabelPrefer = $this->finder->findSellLabels($dataPrefer);


            if ($rtSellLabel[0]['lot_id'] != 0 && $rtSellLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtSellLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtSellLabel); $i++) {
                    $labelFinder['lot_id'] = $rtSellLabel[$i]['lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtSellLabel[$i]['lot_id'];


                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;


                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateSell($sellID, $updateSell);
                }
            }
            if (isset($rtSellLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtSellLabelPrefer); $i++) {
                    $labelFinder['prefer_lot_id'] = $rtSellLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinder['sell_id'] = $data['sell_id'];
                    $rtLabelFromSellLabel = $this->finder->findSellLabels($labelFinder);
                    $rtsellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtSellLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtsellCpoItem[0]['cpo_item_id'];

                    $sumQty = 0;
                    for ($j = 0; $j < count($rtLabelFromSellLabel); $j++) {
                        $sumQty += $rtLabelFromSellLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQty;

                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updateSell['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updateSell($sellID, $updateSell);
                }
            }

            //update stockcontrol, stockitem and lot

            $paramsPackingID['PackingID'] = $packingID;
            $arrPacking = $this->packingFinder->findPackingItem($paramsPackingID);

            $insertStockContol['PackingNo'] = $arrPacking[0]['PackingNo'];
            $stockControlID = $this->stockControlUpdater->insertStockControl($insertStockContol);

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

        $dataSellID['sell_id'] = $data['sell_id'];
        $arrSellLabel = $this->finder->findSellLabels($dataSellID);
        $dataUpdate['up_status'] = "USED";
        $user_id = $this->session->get('user')["id"];
        for ($i = 0; $i < count($arrSellLabel); $i++) {
            $this->labelUpdater->updateLabelStatus($arrSellLabel[$i]['label_id'], $dataUpdate, $user_id);
        }
        $data['up_status'] = "PRINTED";
        $this->updater->updateSellStatus($sellID, $data, $user_id);

        $rtSell = $this->sellFinder->findSells($data);

        $rtSell['printer_id'] = $data['printer_id'];
        $this->updateTag->genTags($sellID, $rtSell);

        $this->tempQueryUpdater->deleteTempQuery((int)$rtSell[0]['product_id']);

        return $this->responder->withRedirect($response, "sells");
    }
}
