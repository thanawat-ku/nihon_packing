<?php

namespace App\Action\Api;

use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SplitLabelRegisterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $labelsUpdater;
    private $labelsFinder;
    private $SpDetailFinder;



    public function __construct(
        SplitLabelDetailFinder $finder,
        SplitLabelUpdater $updater,
        LabelUpdater $labelsUpdater,
        Responder $responder,
        LabelFinder $labelsFinder
    ) {
        $this->finder = $finder;
        $this->responder = $responder;
        $this->labelsUpdater = $labelsUpdater;
        $this->labelsFinder = $labelsFinder;
        $this->updater = $updater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $DataLabel['label_id'] = $params['label_id'];

        $getSpID =  $this->finder->findSplitLabelDetailsForscan($DataLabel);
        $data['split_label_id'] = $getSpID[0]['split_label_id'];
        $findDetail = $this->finder->findSplitLabelDetails($data);


        $labels = [];

        $data2['status'] = "PACKED";
        $labelID =  $DataLabel['label_id'];
        $this->labelsUpdater->updateLabelApi($labelID, $data2, $user_id);

        $count = 0;
        for ($i = 0; $i < sizeof($findDetail); $i++) {
            $DataLabel2['label_id'] = $findDetail[$i]['label_id'];
            $label = $this->labelsFinder->findLabelsForScan($DataLabel2);
            array_push($labels, $label[0]);

            if ($label[0]['status'] == "PACKED") {
                $count++;
            }
        }

        $splitID = $data['split_label_id'];
        if ($count == 2) {
            $this->updater->updateSplitLabelApi($data2, $splitID, $user_id);
        } else if ($count == 1) {
            $data3['status'] = "PACKING";
            $this->updater->updateSplitLabelApi($data3, $splitID, $user_id);
        }

        $rtdata['message'] = "Get SplitLabel Register Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $labels;

        return $this->responder->withJson($response, $rtdata);
    }
}
