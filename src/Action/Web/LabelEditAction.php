<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelEditAction
{
    private $session;
    private $responder;
    private $updater;
    private $finder;
    private $lotFinder;
    private $lotUpdater;

    public function __construct(
        Session $session,
        Responder $responder,
        LabelUpdater $updater,
        LabelFinder $finder,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater
    ) {
        $this->responder = $responder;
        $this->session = $session;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $labelId = $data["id"];

        $findLabel['label_id'] = $labelId;
        $label = $this->finder->findLabelSingleTable($findLabel);

        if ($label[0]['status'] == "CREATED") {
            $this->updater->updateLabel($labelId, $data);
            // $oldLabelQty = $label[0]['quantity'];
            // $lotId['lot_id'] = $label[0]['lot_id'];
            // $lot = $this->lotFinder->findLots($lotId);
            // $oldRealQty = $lot[0]['real_qty'];
            // $newRealqty = ((int)$oldRealQty - (int)$oldLabelQty) + (int)$data['quantity'];
            // $stdPack = $lot[0]['std_pack'];
            // if ($newRealqty >= (int)$stdPack && $label[0]['label_type'] == "NONFULLY") {
            //     $dataLot['real_qty'] = $newRealqty - (int)$stdPack;
            //     $newLabel['lot_id'] = $lotId['lot_id'];
            //     $newLabel['real_qty'] = $newRealqty;
            //     $newLabel['std_pack'] = $stdPack;
            //     $newLabel['product_id'] = $lot[0]['product_id'];
            //     $newLabel['user_id'] = $this->session->get('user')["id"];
            //     $newLabel['printer_id'] = $label[0]['printer_id'];
            //     $newLabel['wait_print'] = "Y";
            //     $this->updater->genLabelNo($newLabel);
            //     $this->lotUpdater->updateLot($lotId['lot_id'], $dataLot);
            // } else {
            //     $dataLot['real_qty'] = $newRealqty;
            //     $this->lotUpdater->updateLot($lotId['lot_id'], $dataLot);
            // }
        }
        if ($data['from'] == "label_lot") {
            $lotId = $label[0]['lot_id'];
            $viewData = [
                'id' => $lotId,
            ];
            return $this->responder->withRedirect($response, "label_lot", $viewData);
        } else if ($data['from'] == "label_split") {
            $splitId = $data['split_id'];
            $viewData = [
                'id' => $splitId,
            ];
            return $this->responder->withRedirect($response, "label_splitlabel", $viewData);
        } else if ($data['from'] == "label_split") {
            $splitId = $data['split_id'];
            $viewData = [
                'id' => $splitId,
            ];
            return $this->responder->withRedirect($response, "label_splitlabel", $viewData);
        } else if ($data['from'] == "label_merge") {
            $mergeId = $data['merge_id'];
            $viewData = [
                'id' => $mergeId,
            ];
            return $this->responder->withRedirect($response, "label_merge_news", $viewData);
        } else {
            return $this->responder->withRedirect($response, "labels");
        }
    }
}
