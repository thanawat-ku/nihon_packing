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
final class LabelScanSplitAction
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
        SplitLabelDetailFinder $splitDetailFinder,
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

        $label = $this->finder->findLabelSingleTable($labelNO);

        if ($label[0]['status'] == "PACKED") {
            $rtdata['message'] = "find Label for split Successful";
            $rtdata['error'] = false;
            $rtdata['labels'] = $label;
        } else {
            $rtdata['message'] = "find Label for scan fail";
            $rtdata['error'] = true;
            $rtdata['labels'] = "error";
        }
        
        return $this->responder->withJson($response, $rtdata);
    }
}
