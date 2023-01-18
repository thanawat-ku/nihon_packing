<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotReverseAction
{
    private $responder;
    private $updater;
    private $lotUpdater;
    private $finder;
    private $lotFinder;
    private $lNFPFinder;
    private $session;

    public function __construct(
        Session $session,
        Responder $responder,
        LabelUpdater $updater,
        LotUpdater $lotUpdater,
        LabelFinder $finder,
        LotFinder $lotFinder,
        LotNonFullyPackFinder $lNFPFinder,
    ) {
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->lotUpdater = $lotUpdater;
        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->lNFPFinder = $lNFPFinder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $lotId = $data["lot_id"];
        $params['lot_id'] = $lotId;
        $params['lot_id'] = $lotId;

        //find merge nonfully labels and return to their lots
        $searchLNFP['search_prefer_lot_id'] = true;
        $searchLNFP['prefer_lot_id'] = $lotId;
        $rtLNFP = $this->lNFPFinder->findLotNonFullyPacks($searchLNFP);
         //fix error on sql server 
         if (isset($rtLNFP)) {
            for ($i = 0; $i < count($rtLNFP); $i++) {
                $lotIDInLabel = $rtLNFP[$i]['lot_id_in_label'];
                $labelID = $rtLNFP[$i]['label_id'];
                $labelQty = $rtLNFP[$i]['quantity'];

                //find lot nsp
                $searchLotNsp['LotID'] = $lotIDInLabel;
                $rtLotNsp = $this->lotFinder->findLotNsps($searchLotNsp);

                //update current to nsp
                $dataLotNsp2['real_qty'] = $rtLotNsp[0]['CurrentQty'] + $labelQty;
                $this->lotUpdater->updateLotNsp($lotIDInLabel, $dataLotNsp2);

                //find lot packing
                $searchLotPacking['lot_id'] = $lotIDInLabel;
                $rtLotPacking = $this->lotFinder->findLots($searchLotPacking);

                //update real_qty and real_lot_qty to packing
                $dataLotPacking['real_qty'] = $rtLotPacking[0]['real_qty'] + $labelQty;
                $dataLotPacking['real_lot_qty'] = $rtLotPacking[0]['real_lot_qty'] + $labelQty;
                $this->lotUpdater->updateLot($lotIDInLabel, $dataLotPacking);

                //void label
                $upDateLabel['up_status'] = 'PACKED';
                $user_id = $this->session->get('user')["id"];
                $this->updater->updateLabelStatus($labelID, $upDateLabel, $user_id);
            }
        }

        //find labels and void
        $labels = $this->finder->findLabels($params);
        for ($i = 0; $i < sizeof($labels); $i++) {
            $labelId = $labels[$i]['id'];
            $labelData['is_delete'] = "Y";
            $labelData['status'] = "VOID";
            $this->updater->updateLabel($labelId, $labelData);
        }
        $lotData['generate_lot_no'] = "";
        $lotData['status'] = "CREATED";
        $this->lotUpdater->updateLot($lotId, $lotData);
        return $this->responder->withRedirect($response, "lots");
    }
}
