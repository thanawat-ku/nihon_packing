<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelScanSplitRegisterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $splitDetailFinder;
    private $splitFinder;
    private $lotFinder;
    private $productFinder;

    public function __construct(
        LabelFinder $finder,
        LotFinder $lotFinder,
        ProductFinder $productFinder,
        LabelUpdater $updater,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        SplitLabelFinder $splitFinder
    ) {

        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->splitFinder = $splitFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $labelNO['label_no'] = $data['label_no'];

        if ($data['split_label_id'] == "0") {
            $label = $this->splitDetailFinder->findSplitLabelDetailsForscan($labelNO);
            $label[0]['id'] = $label[0]['label_id'];
            $splitId1 = $label[0]['split_label_id'];
            $data2['split_label_id'] = $splitId1;
            $split = $this->splitFinder->findSplitLabels($data2);
            if ($label[0]['status'] == "PRINTED" && ($split[0]['status'] == "PRINTED" || $split[0]['status'] == "PACKING")) {
                $rtdata['message'] = "find Label for split Successful";
                $rtdata['error'] = false;
                $rtdata['labels'] = $label;
            } else {
                $rtdata['message'] = "find Label for scan fail";
                $rtdata['error'] = true;
                $rtdata['labels'] = "error";
            }
        } else {
            $splitId1 = $data['split_label_id'];
            $splitDetail = $this->splitDetailFinder->findSplitLabelDetailsForscan($labelNO);
            $data2['split_label_id'] = $splitId1;
            $split = $this->splitFinder->findSplitLabels($data2);
            if (($splitDetail[0]['split_label_id'] == $splitId1 && $splitDetail[0]['status'] == "PRINTED") && ($split[0]['status'] == "PRINTED" || $split[0]['status'] == "PACKING")) {
                $splitDetail[0]['id'] = $splitDetail[0]['label_id'];
                $rtdata['message'] = "find Label for split Successful";
                $rtdata['error'] = false;
                $rtdata['labels'] = $splitDetail;
            } else {
                $rtdata['message'] = "find Label for scan fail";
                $rtdata['error'] = true;
                $rtdata['labels'] = "error";
            }
        }


        return $this->responder->withJson($response, $rtdata);
    }
}
