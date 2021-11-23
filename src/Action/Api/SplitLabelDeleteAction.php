<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class SplitLabelDeleteAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $finder;
    private $updaterLabel;
    private $updaterSpiteLabelDetail;
    private $finderSpiteLabelDetail;

    public function __construct(
        SplitLabelFinder $finder,
        Responder $responder,
        LabelUpdater $updaterLabel,
        SplitLabelUpdater  $updater,
        SplitLabelDetailUpdater $updaterSpiteLabelDetail,
        SplitLabelDetailFinder $finderSpiteLabelDetail,
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
        $this->finderSpiteLabelDetail = $finderSpiteLabelDetail;
        $this->updaterSpiteLabelDetail = $updaterSpiteLabelDetail;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $labelID = $params['label_id'];
        $splitID = $params['split_label_id'];

        $splitLabel = $this->finder->findSplitLabels($params);
        if ($splitLabel[0]['status'] == "CREATED") {
            $dataLabel['status'] = "PACKED";
            $this->updaterLabel->updateLabelApi($labelID, $dataLabel, $user_id);

            $dataFindSplit['split_label_id'] = $splitID;
            $splitDetail = $this->finderSpiteLabelDetail->findSplitLabelDetails($dataFindSplit);

            for ($i = 0; $i < sizeof($splitDetail); $i++) {
                $labelID2 = $splitDetail[$i]['label_id'];
                $this->updaterLabel->deleteLabel($labelID2, $params);
            }
            $this->updater->deleteSplitLabel($splitID, $params);

            $rtdata['message'] = "Delete Splitlabel Successful";
            $rtdata['error'] = false;
            $rtdata['split_labels'] = $this->finder->findSplitLabels($params);
            
        } else {
            $rtdata['message'] = "Delete Splitlabel fail";
            $rtdata['error'] = true;
            $rtdata['split_labels'] = $this->finder->findSplitLabels($params);
        }


        return $this->responder->withJson($response, $rtdata);
    }
}
