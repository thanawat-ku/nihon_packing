<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
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
    private $updaterLabel;
    private $updaterSpiteLabelDetail;

    public function __construct(
        SplitLabelFinder $finder,
        Responder $responder,
        LabelUpdater $updaterLabel,
        SplitLabelUpdater  $updater,
        SplitLabelDetailUpdater $updaterSpiteLabelDetail,
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
        $this->updaterSpiteLabelDetail = $updaterSpiteLabelDetail;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $labelID = $params['label_id'];
        //insertSplitLabel แต่ยังไม่ split_label_no 
        $this->updater->insertSplitLabelApi($params, $user_id);

        $data = $this->finder->findSplitLabels($params);
        //สร้าง split_label_no จากID และ LabelID 
        $params['split_label_no'] = "SP{$data[0]['id']}LB{$data[0]['label_id']}";
        $splitID = $data[0]['id'];
        $this->updater->updateSplitLabelApi($params, $splitID, $user_id);

        $dataLabel['split_label_id'] = $splitID;
        $dataLabel['status'] = "VOID";

        $this->updaterLabel->updateLabelApi($labelID, $dataLabel, $user_id);

        $labelDetail = $this->updaterLabel->genSplitLabel($params);

        for ($i = 0; $i < sizeof($labelDetail); $i++) {
            $dataDetailSL['label_id'] = $labelDetail[$i]['id'];
            $dataDetailSL['split_label_id'] = $splitID;
            $this->updaterSpiteLabelDetail->insertSplitLabelDetailDeatilApi($dataDetailSL,$user_id);
        }

        $rtdata['message'] = "Add Splitlabel Successful";
        $rtdata['error'] = false;
        $rtdata['split_labels'] = $this->finder->findSplitLabels($params);

        return $this->responder->withJson($response, $rtdata);
        
    }
}
