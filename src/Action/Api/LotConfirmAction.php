<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotConfirmAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $labelUpdater;
    private $lNFPFinder;

    public function __construct(LotFinder $finder, ProductFinder $productFinder, 
    Responder $responder, LotUpdater $updater, LabelUpdater $labelUpdater,
    LotNonFullyPackFinder $lNFPFinder)
    {
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->lNFPFinder = $lNFPFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        
        $params = (array)$request->getQueryParams();
        $user_id = $params["user_id"];
        $lotID = $params["lot_id"];
        $findlot['lot_id'] = $lotID;
        $lot = $this->finder->findLots($findlot);

        $params['real_loy_qty'] = $params['real_qty']; //เปลี่ยนค่า real_qty เดิม เป็น 'real_lot_qty'
        $realQty = $params['real_qty'] + $params['lNFPQty']; //จำนวนของ lot_real_qty + จำนวน label ที่ merge มา;

        $params['real_qty'] = $realQty; //เปลี่ยนแปลง real_qty เดิม ที่มีการบวกการ merge เข้ามาด้วย
        

        if ($lot[0]['status'] == "CREATED") {
            $params['product_id'] = $lot[0]['product_id'];
            $params['status'] = "PRINTED";
            $params['wait_print'] = "Y";

            $genLabelSuccess = $this->labelUpdater->genLabelNo($params);

            $params['generate_lot_no'] = "L" . str_pad($lotID, 11, "0", STR_PAD_LEFT);
            $this->updater->confirmLotApi($lotID, $params, $user_id);
            $dataLot['real_qty'] = $params['real_qty'];
            $this->updater->updateLotNsp($lotID, $dataLot);

            $dataLotNsp['real_qty'] = $realQty;
            $this->updater->updateLotNsp($lotID, $dataLotNsp);

            //find lot_id in label
            $searchLNFP['lot_id'] = $lotID;
            $rtLNFP = $this->lNFPFinder->findLotNonFullyPacks($searchLNFP);

            //fix error on sql server 
            if (isset($rtLNFP)) {
                for ($i = 0; $i < count($rtLNFP); $i++) {
                    $lotIDInLabel = $rtLNFP[$i]['lot_id_in_label'];
                    $labelID = $rtLNFP[$i]['label_id'];
                    $labelQty = $rtLNFP[$i]['quantity'];

                    //find lot nsp
                    $searchLotNsp['LotID'] = $lotIDInLabel;
                    $rtLotNsp = $this->finder->findLotNsps($searchLotNsp);

                    //update current to nsp
                    $dataLotNsp2['real_qty'] = $rtLotNsp[0]['CurrentQty'] - $labelQty;
                    $this->updater->updateLotNsp($lotIDInLabel, $dataLotNsp2);

                    //find lot packing
                    $searchLotPacking['lot_id'] = $lotIDInLabel;
                    $rtLotPacking = $this->finder->findLots($searchLotPacking);

                    //update real_qty and real_lot_qty to packing
                    $dataLotPacking['real_qty'] = $rtLotPacking[0]['real_qty'] - $labelQty;
                    $dataLotPacking['real_lot_qty'] = $rtLotPacking[0]['real_lot_qty'] - $labelQty;
                    $this->updater->updateLot($lotIDInLabel, $dataLotPacking);

                    //void label
                    $upDateLabel['up_status'] = 'VOID';
                    $upDateLabel['void'] = 'MERGED';
                    $user_id = $this->session->get('user')["id"];
                    $this->labelUpdater->updateLabelStatus($labelID, $upDateLabel, $user_id);
                }
            }

            $rtdata['message'] = "Confirm Lot Successful";
            $rtdata['error'] = false;
            $rtdata['lots'] = $this->finder->findLots($findlot);

        } else {
            $rtdata['message'] = "Confirm Lot Fail";
            $rtdata['error'] = true;
            $rtdata['lots'] = $this->finder->findLots($findlot);
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
