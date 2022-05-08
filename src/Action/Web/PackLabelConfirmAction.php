<?php

namespace App\Action\Web;

use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use App\Domain\Pack\Service\PackFinder;
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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackLabelConfirmAction
{
    private $responder;
    private $finder;
    private $packCpoItemFinder;
    private $updater;
    private $labelFinder;
    private $labelUpdater;
    private $session;
    private $packFinder;
    private $updateTag;
    private $packingFinder;
    private $packingUpdate;
    private $packingItemUpdate;
    private $tempQueryUpdater;
    private $stockControlUpdater;
    private $stockItemUpdater;
    private $lotFinder;
    private $lotUpdater;
    private $cpoItemFinder;
    private $cpoItemUpdater;

    public function __construct(
        Responder $responder,
        PackLabelFinder $finder,
        PackCpoItemFinder $packCpoItemFinder,
        PackUpdater $updater,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        PackFinder $packFinder,
        TagUpdater $updateTag,
        PackingFinder $packingFinder,
        PackingUpdater $packingUpdate,
        PackingItemUpdater $packingItemUpdate,
        TempQueryUpdater $tempQueryUpdater,
        StockControlUpdater $stockControlUpdater,
        StockItemUpdater $stockItemUpdater,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater,
        CpoItemFinder $cpoItemFinder,
        CpoItemUpdater $cpoItemUpdater,
        Session $session
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->packCpoItemFinder = $packCpoItemFinder;
        $this->updater = $updater;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
        $this->packFinder = $packFinder;
        $this->updateTag = $updateTag;
        $this->packingFinder = $packingFinder;
        $this->packingUpdate = $packingUpdate;
        $this->packingItemUpdate = $packingItemUpdate;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->stockControlUpdater = $stockControlUpdater;
        $this->stockItemUpdater = $stockItemUpdater;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
        $this->cpoItemFinder = $cpoItemFinder;
        $this->cpoItemUpdater = $cpoItemUpdater;
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

        $packID = (int)$data['pack_id'];

        $user_id = $this->session->get('user')["id"];

        $dt = date('Y-m-d');
        $time  = strtotime($dt);
        $month = date('m', $time);
        $year  = date('Y', $time);
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $params['startDate'] = date('Y-' . $month . '-01');
        $params['endDate'] = date('Y-' . $month . '-' . $day);

        //test date empty
        // $params['startDate'] = date('Y-' .'07' . '-01');
        // $params['endDate'] = date('Y-' . '07' . '-' . 30);

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
            $rtPackLabel = $this->finder->findPackLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['pack_id'] = $data['pack_id'];
            $rtPackLabelPrefer = $this->finder->findPackLabels($dataPrefer);

            if (isset($rtPackLabel[0]['lot_id']) != 0 && $rtPackLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtPackLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtPackLabel); $i++) {
                    $labelFinder['lot_id'] = $rtPackLabel[$i]['lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->packCpoItemFinder->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtPackLabel[$i]['lot_id'];

                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQtyLot = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQtyLot += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQtyLot;


                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updatePack($packID, $updatePack);
                }
            }
            if (isset($rtPackLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtPackLabelPrefer); $i++) {
                    $labelFinderP['prefer_lot_id'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinderP['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinderP);
                    $rtpackCpoItem = $this->packCpoItemFinder->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQtyPre = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQtyPre += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQtyPre;

                    // update packing item when lot id and prefer lot id duplicate

                    if (!isset($rtPackLabel[0]) == 1) {
                        $this->packingItemUpdate->insertPackingItem($isPackingItem);
                    } else {
                        for ($k = 0; $k < count($rtPackLabel); $k++) {
                            if ($labelFinderP['prefer_lot_id'] == $rtPackLabel[$k]['lot_id']) {
                                $labelFinderLP['lot_id'] = $rtPackLabel[$k]['lot_id'];
                                $labelFinderLP['pack_id'] = $data['pack_id'];
                                $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinderLP);

                                $sumQtyLP = 0;
                                for ($l = 0; $l < count($rtLabelFromPackLabel); $l++) {
                                    $sumQtyLP += $rtLabelFromPackLabel[$l]['quantity'];
                                }
                                $quantityPackingItem['Quantity'] = $sumQtyPre + $sumQtyLP;
                                $this->packingItemUpdate->updatePackingItem($packingID, (int)$labelFinderP['prefer_lot_id'], $quantityPackingItem);
                            } else {
                                $this->packingItemUpdate->insertPackingItem($isPackingItem);
                            }
                        }
                    }

                    //###################################################################################

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updatePack($packID, $updatePack);
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
            $rtPackLabel = $this->finder->findPackLabels($data);
            $dataPrefer['find_prefer_lot_id'] = true;
            $dataPrefer['pack_id'] = $data['pack_id'];
            $rtPackLabelPrefer = $this->finder->findPackLabels($dataPrefer);


            if ($rtPackLabel[0]['lot_id'] != 0 && $rtPackLabel[0]['label_type'] != "MERGE_NONFULLY" && $rtPackLabel[0]['label_type'] != "MERGE_FULLY") {
                for ($i = 0; $i < count($rtPackLabel); $i++) {
                    $labelFinder['lot_id'] = $rtPackLabel[$i]['lot_id'];
                    $labelFinder['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinder);
                    $rtpackCpoItem = $this->packCpoItemFinder->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;

                    $isPackingItem['LotID'] = $rtPackLabel[$i]['lot_id'];

                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQtyLot = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQtyLot += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQtyLot;


                    $this->packingItemUpdate->insertPackingItem($isPackingItem);

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updatePack($packID, $updatePack);
                }
            }
            if (isset($rtPackLabelPrefer[0]['prefer_lot_id']) != 0) {
                for ($i = 0; $i < count($rtPackLabelPrefer); $i++) {
                    $labelFinderP['prefer_lot_id'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $labelFinderP['pack_id'] = $data['pack_id'];
                    $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinderP);
                    $rtpackCpoItem = $this->packCpoItemFinder->findPackCpoItems($data);

                    $isPackingItem['PackingID'] = $packingID;
                    $isPackingItem['InvoiceItemID'] = 0;
                    $isPackingItem['LotID'] = $rtPackLabelPrefer[$i]['prefer_lot_id'];
                    $isPackingItem['CpoItemID'] = $rtpackCpoItem[0]['cpo_item_id'];

                    $sumQtyPre = 0;
                    for ($j = 0; $j < count($rtLabelFromPackLabel); $j++) {
                        $sumQtyPre += $rtLabelFromPackLabel[$j]['quantity'];
                    }
                    $isPackingItem['Quantity'] = $sumQtyPre;

                    // update packing item when lot id and prefer lot id duplicate

                    if (!isset($rtPackLabel[0]) == 1) {
                        $this->packingItemUpdate->insertPackingItem($isPackingItem);
                    } else {
                        for ($k = 0; $k < count($rtPackLabel); $k++) {
                            if ($labelFinderP['prefer_lot_id'] == $rtPackLabel[$k]['lot_id']) {
                                $labelFinderLP['lot_id'] = $rtPackLabel[$k]['lot_id'];
                                $labelFinderLP['pack_id'] = $data['pack_id'];
                                $rtLabelFromPackLabel = $this->finder->findPackLabels($labelFinderLP);

                                $sumQtyLP = 0;
                                for ($l = 0; $l < count($rtLabelFromPackLabel); $l++) {
                                    $sumQtyLP += $rtLabelFromPackLabel[$l]['quantity'];
                                }
                                $quantityPackingItem['Quantity'] = $sumQtyPre + $sumQtyLP;
                                $this->packingItemUpdate->updatePackingItem($packingID, (int)$labelFinderP['prefer_lot_id'], $quantityPackingItem);
                            } else {
                                $this->packingItemUpdate->insertPackingItem($isPackingItem);
                            }
                        }
                    }


                    //###################################################################################

                    $updatePack['packing_id'] =  $isPackingItem['PackingID'];
                    $this->updater->updatePack($packID, $updatePack);
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
        $arrPackLabel = $this->finder->findPackLabels($dataPackID);
        $dataUpdate['up_status'] = "USED";
        $user_id = $this->session->get('user')["id"];
        for ($i = 0; $i < count($arrPackLabel); $i++) {
            $this->labelUpdater->updateLabelStatus($arrPackLabel[$i]['label_id'], $dataUpdate, $user_id);
        }
        $data['up_status'] = "PRINTED";
        $this->updater->updatePackStatus($packID, $data, $user_id);

        $rtPack = $this->packFinder->findPacks($data);

        $rtPack['printer_id'] = $data['printer_id'];
        $this->updateTag->genTags($packID, $rtPack);

        $this->tempQueryUpdater->deleteTempQuery((int)$rtPack[0]['product_id']);

        return $this->responder->withRedirect($response, "packs");
    }
}
