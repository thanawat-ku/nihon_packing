<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class SplitLabelAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $finder;
    private $labelFinder;
    private $updaterLabel;
    private $updaterSpiteLabelDetail;

    public function __construct(
        SplitLabelFinder $finder,
        Responder $responder,
        LabelUpdater $updaterLabel,
        SplitLabelUpdater  $updater,
        SplitLabelDetailUpdater $updaterSpiteLabelDetail,
        LabelFinder $labelFinder,
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->labelFinder = $labelFinder;
        $this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
        $this->updaterSpiteLabelDetail = $updaterSpiteLabelDetail;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $labelID = $params['label_id'];

        $label =  $this->labelFinder->findLabelSingleTable($params);
        //validate data for split
        if ($label[0]['status'] == "PACKED") {
            $splitID = $this->updater->insertSplitLabelApi($params, $user_id);

            $dataLabel['split_label_id'] = $splitID;
            $dataLabel['label_void_reason_id'] = "1";
            $dataLabel['status'] = "VOID";

            $this->updaterLabel->updateLabelApi($labelID, $dataLabel, $user_id);

            $labelDetail = $this->updaterLabel->genSplitLabel($params);

            for ($i = 0; $i < sizeof($labelDetail); $i++) {
                $dataDetailSL['label_id'] = $labelDetail[$i]['id'];
                $dataDetailSL['split_label_id'] = $splitID;
                $this->updaterSpiteLabelDetail->insertSplitLabelDetailDeatilApi($dataDetailSL, $user_id);
            }

            $rtdata['message'] = "Add Splitlabel Successful";
            $rtdata['error'] = false;
            $rtdata['split_labels'] = $this->finder->findSplitLabels($params);
        } else {
            $rtdata['message'] = "Add Splitlabel Fail";
            $rtdata['error'] = true;
            $rtdata['split_labels'] = $this->finder->findSplitLabels($params);
        }


        return $this->responder->withJson($response, $rtdata);
    }
}
