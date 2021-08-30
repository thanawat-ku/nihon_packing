<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class SplitLabelCreateLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $updaterLabel; 
    private $LabelFinder;
    private $finder;

    public function __construct(
        SplitLabelFinder $finder,
        LabelFinder $LabelFinder,
        Responder $responder,
        LabelUpdater $updaterLabel,
        SplitLabelUpdater  $updater,
        
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        //$this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
        $this->$LabelFinder = $LabelFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $labelID = $params['label_id'];
        $lotID = $params['lot_id'];
        $status = $params['status'];
        $type = $params['label_type'];
        $dataSplitlabel['label_id'] = $labelID;

        $data1 = $this->finder->findSplitLabels($dataSplitlabel);
        $dataLabel['split_label_id'] = $data1[0]['id'];
        $sliteLabelID = $dataLabel['split_label_id'];
        $dataLabel['lot_id'] = $lotID;
        $dataLabel['label_type'] = $type;
        $dataLabel['status'] = $status;
        $labelNo = "LBSP{$labelID}{$sliteLabelID}";

        for ($i = 0; $i < 2; $i++) {
            $dataLabel['label_no'] = $labelNo . str_pad($i, 3, "0", STR_PAD_LEFT);
            $this->updaterLabel->insertLabelApi($dataSplitlabel, $user_id);
        }

        $rtdata['message'] = "Gen label Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $this->LabelFinder->findLabels($dataSplitlabel);

        return $this->responder->withJson($response, $rtdata);
    }
}
