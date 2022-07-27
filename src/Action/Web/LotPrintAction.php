<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotPrintAction
{
    private $responder;
    private $updater;
    private $finder;
    private $labelUpdater;
    private $lNFPFinder;
    private $session;
    public function __construct(
        Session $session,
        Responder $responder,
        LotUpdater $updater,
        LotFinder $finder,
        LabelUpdater $labelUpdater,
        LotNonFullyPackFinder $lNFPFinder,
    ) {
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->labelUpdater = $labelUpdater;
        $this->lNFPFinder = $lNFPFinder;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data["id"];
        $realLotQty = $data['real_lot_qty'];
        $realQty = $data['real_lot_qty'] + $data['lNFPQty']; //จำนวนของ lot_real_qty + จำนวน label ที่ merge มา;
        $printerID = $data['printer_id'];

        // generate labels
        $params["lot_id"] = $lotId;
        $lots = $this->finder->findLots($params);

        if ($lots[0]['status'] == "CREATED") {
            $data['user_id'] = $this->session->get('user')["id"];
            $data['lot_id'] = $lotId;
            $data['product_id'] = $lots[0]['product_id'];
            $data['real_lot_qty'] = $realLotQty;
            $data['real_qty'] = $realQty;
            $data['std_pack'] = $lots[0]['std_pack'];
            $data['printer_id'] = $printerID;
            $data['wait_print'] = "Y";
            $this->labelUpdater->genLabelNo($data);
            $dataLot['status'] = "PRINTED";
            $dataLot['real_lot_qty'] = $realLotQty; //lot_real_qty แทน real_qty
            $dataLot['real_qty'] = $realQty;
            $dataLot['generate_lot_no'] = "L" . str_pad($lotId, 11, "0", STR_PAD_LEFT);
            $dataLot['printed_user_id'] =  $data['user_id'];
            $this->updater->updateLot($lotId, $dataLot);

            $dataLotNsp['real_qty'] = $realQty;
            $this->updater->updateLotNsp($lotId, $dataLotNsp);

            //find lot_id in label
            $searchLNFP['lot_id'] = $lotId;
            $rtLNFP = $this->lNFPFinder->findLotNonFullyPacks($searchLNFP);

            for ($i = 0; $i < count($rtLNFP); $i++) {
                $lotIDInLabel = $rtLNFP[$i]['lot_id_in_label'];
                $labelID = $rtLNFP[$i]['label_id'];
                $labelQty = $rtLNFP[$i]['quantity'];

                //find lot nsp
                $searchLotNsp['LotID']=$lotIDInLabel;
                $rtLotNsp = $this->finder->findLotNsps($searchLotNsp);
                
                //update current to nsp
                $dataLotNsp2['real_qty'] = $rtLotNsp[0]['CurrentQty'] - $labelQty; 
                $this->updater->updateLotNsp($lotIDInLabel, $dataLotNsp2);

                //find lot packing
                $searchLotPacking['lot_id']=$lotIDInLabel;
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


        // Build the HTTP response
        return $this->responder->withRedirect($response, "lots");
    }
}
