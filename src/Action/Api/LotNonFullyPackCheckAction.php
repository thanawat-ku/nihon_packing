<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotNonFullyPackCheckAction
{
    private $responder;
    private $labelFinder;
    private $labelUpdater;
    private $lnfpUpdater;

    public function __construct(
        Responder $responder,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        LotNonFullyPackUpdater $lnfpUpdater,

    ) {
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $labels = $data['labels'];
        $lotID = $data['lot_id'];
        $user_id = $data['user_id'];
        $labelExp = explode("#", $labels);

        $rtLabel = [];
        $mergeQty = 0;
        foreach ($labelExp as $label) {
            if (strlen($label) == 12) {
                $searchLabel['label_no'] = $label;
                $labelRow = $this->labelFinder->findLabelSingleTable($searchLabel);

                

                if ($labelRow[0]['status'] == 'PACKED' && $labelRow[0]['label_type'] == "NONFULLY") {
                     
                    $upStatus['status'] = 'MERGED';
                    $this->labelUpdater->updateLabelStatus(intval($labelRow[0]['id']), $upStatus, $user_id);
                    $labelRow[0]['check_correct'] = true;
                } else {
                    $labelRow[0]['check_correct'] = false;
                }
                $mergeQty += $labelRow[0]['quantity'];
                array_push($rtLabel, $labelRow[0]);
            }
        }

        $rtdata['message'] = "Get Lot Non Fully Pack Successful";
        $rtdata['error'] = false;
        $rtdata['lot_non_fully_packs'] = $rtLabel;
        $rtdata['merge_qty'] = $mergeQty;

        return $this->responder->withJson($response, $rtdata);
    }
}
