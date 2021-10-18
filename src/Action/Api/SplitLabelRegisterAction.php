<?php

namespace App\Action\Api;

use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
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
    private $SpDetailFinder;



    public function __construct(
        SplitLabelDetailFinder $finder,
        SplitLabelUpdater $updater,
        LabelUpdater $labelsUpdater,
        Responder $responder,
    ) {
        $this->finder = $finder;
        $this->responder = $responder;
        $this->labelsUpdater = $labelsUpdater;
        $this->updater = $updater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $fDetail['split_label_id'] = $params['split_label_id'];
        $dataL['label_id'] = $params['label_id'];
        if ($fDetail['split_label_id'] == "0") {
            $getSpID =  $this->finder->findSplitLabelDetailsForscan($dataL);
            $fDetail['split_label_id'] = $getSpID[0]['split_label_id'];
            $findDetail = $this->finder->findSplitLabelDetails($fDetail);
        } else {
            $findDetail = $this->finder->findSplitLabelDetails($fDetail);
        }


        $labels = [];

        for ($i = 0; $i < sizeof($findDetail); $i++) {
            $dataL['status'] = "PACKED";
            $labelID = $findDetail[$i]['label_id'];
            $splitID = $fDetail['split_label_id'];
            $label = $this->labelsUpdater->updateLabelApi($labelID, $dataL, $user_id);
            array_push($labels, $label[0]);
            $this->updater->updateSplitLabelApi($dataL, $splitID, $user_id);
        }


        $rtdata['message'] = "Get SplitLabelRegister Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $labels;

        return $this->responder->withJson($response, $rtdata);
    }
}