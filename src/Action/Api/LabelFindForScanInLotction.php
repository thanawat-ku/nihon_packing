<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelFindForScanInLotction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $splitDetailFinder;
    private $lotFinder;

    public function __construct(
        LabelFinder $finder,
        LotFinder $lotFinder,
        ProductFinder $productFinder,
        LabelUpdater $updater,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder
    ) {

        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
        $this->splitDetailFinder = $splitDetailFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {


        $data = (array)$request->getQueryParams();
        $labelNO['label_no'] = $data['label_no'];
        $findlabel = $this->finder->findLabelsForScan($labelNO);
        $lotId['lot_id'] = $findlabel[0]['lot_id'];
        $findlabel[0]['generate_lot_no'] = "None";

        if ($findlabel[0]['status'] == "PRINTED") {
            $lot = $this->lotFinder->findLots($lotId);
            if ($lot[0]['status'] == "PRINTED" && $lotId['lot_id'] == $data['lot_id']) {
                $findlabel[0]['generate_lot_no'] = $lot[0]['generate_lot_no'];
                $rtdata['message'] = "find Label for scan Successful";
                $rtdata['error'] = false;
                $rtdata['labels'] = $findlabel;
            } else {
                $rtdata['message'] = "find Label for scan fail";
                $rtdata['error'] = true;
                $rtdata['labels'] = "error";
            }
        } else {
            $rtdata['message'] = "find Label for scan fail";
            $rtdata['error'] = true;
            $rtdata['labels'] = "error";
        }


        return $this->responder->withJson($response, $rtdata);
    }
}
