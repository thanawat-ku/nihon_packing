<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelUpdater;

/**
 * Action.
 */
final class LotPrintAction
{
    private $responder;
    private $updater;
    private $finder;
    private $labelUpdater;
    public function __construct(
        Responder $responder,
        LotUpdater $updater,
        LotFinder $finder,
        LabelUpdater $labelUpdater
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->labelUpdater = $labelUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data["id"];
        $realQty = $data['real_qty'];

        // generate labels
        $params["lot_id"] = $lotId;
        $lots = $this->finder->findLots($params);

        if ($lots[0]['status'] == "CREATED") {
            // $merge_status = (string)($data['merge_status'] ?? '');
            $lot_id = (int)($lots[0]['id'] ?? 1);
            $real_qty = (int)$realQty ?? 1;
            $std_pack = (int)$lots[0]['std_pack'] ?? 1;
            $num_packs = ceil($real_qty / $std_pack);
            $num_full_packs = floor($real_qty / $std_pack);

            $labels = [];
            for ($i = 0; $i < $num_full_packs; $i++) {
                $data1['id']  = $lot_id;
                $data1['label_no'] = "X" . str_pad($i, 11, "0", STR_PAD_LEFT);
                $data1['label_type'] = "FULLY";
                $data1['quantity'] = $std_pack;
                $data1['status'] = "CREATED";
                $id = $this->labelUpdater->insertLabel($data1);
                $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->labelUpdater->updateLabel($id, $data1);
                $label['id'] = $id;
                $label['label_no'] = $data1['label_no'];
                $label['label_type'] = $data1['label_type'];
                $label['quantity'] = $data1['quantity'];
                array_push($labels, $label);
            }
            if ($num_full_packs != $num_packs) {
                $data1['id']  = $lot_id;
                $data1['label_no'] = "X" . str_pad($i, 11, "0", STR_PAD_LEFT);
                $data1['label_type'] = "NONFULLY";
                $data1['quantity'] = $real_qty - ($num_full_packs * $std_pack);
                $data1['status'] = "CREATED";
                $id = $this->labelUpdater->insertLabel($data1);
                $data1['label_no'] = "P" . str_pad($id, 11, "0", STR_PAD_LEFT);
                $this->labelUpdater->updateLabel($id, $data1);
                $label['id'] = $id;
                $label['label_no'] = $data1['label_no'];
                $label['label_type'] = $data1['label_type'];
                $label['quantity'] = $data1['quantity'];
                array_push($labels, $label);
            }
            
            $this->updater->printLot($lotId);
        }


        // Build the HTTP response
        return $this->responder->withRedirect($response, "lots");
    }
}
