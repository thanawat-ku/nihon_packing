<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelFindForScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        LabelFinder $finder,
        ProductFinder $productFinder,
        LabelUpdater $updater,
        Responder $responder
    ) {

        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {


        $data = (array)$request->getQueryParams();
        $labelNO['label_no'] = $data['label_no'];
        $status =  $data['status'];
        $findlabel = $this->finder->findLabelsForScan($labelNO);

        if ($findlabel) {

            if ($findlabel[0]['status'] == $status) {
                $rtdata['message'] = "find Label for scan Successful";
                $rtdata['error'] = false;
                $rtdata['labels'] = $findlabel;
            } else {
                $rtdata['message'] = "find Label for scan fail becuse not CONFIRM";
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
