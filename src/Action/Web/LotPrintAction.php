<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelUpdater;
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
    private $session;
    public function __construct(
        Session $session,
        Responder $responder,
        LotUpdater $updater,
        LotFinder $finder,
        LabelUpdater $labelUpdater
    ) {
        $this->session = $session;
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
        $printerID = $data['printer_id'];

        // generate labels
        $params["lot_id"] = $lotId;
        $lots = $this->finder->findLots($params);

        if ($lots[0]['status'] == "CREATED") {
            $data['user_id'] = $this->session->get('user')["id"];
            $data['lot_id'] = $lotId;
            $data['product_id'] = $lots[0]['product_id'];
            $data['real_qty'] = $realQty;
            $data['std_pack'] = $lots[0]['std_pack'];
            $data['printer_id'] = $printerID;
            $data['wait_print'] = "Y";
            $this->labelUpdater->genLabelNo($data);
            $dataLot['status'] = "PRINTED";
            $dataLot['real_qty'] = $realQty;
            $dataLot['generate_lot_no'] = "L" . str_pad($lotId, 11, "0", STR_PAD_LEFT);
            $dataLot['printed_user_id'] =  $data['user_id'];

            $this->updater->updateLot($lotId, $dataLot);
        }


        // Build the HTTP response
        return $this->responder->withRedirect($response, "lots");
    }
}
