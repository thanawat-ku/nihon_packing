<?php

namespace App\Action\Api;

use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SplitLabelDetailAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $finderLabels;
    private $updater;

    public function __construct(SplitLabelDetailFinder $finder, SplitLabelDetailUpdater $updater, LabelFinder $finderLabels, Responder $responder)
    {
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finderLabels = $finderLabels;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $dataL = $params["label_id"];
        $findLabels = $this->finderLabels->findLabels($dataL);
        for ($i = 0; $i < sizeof($findLabels); $i++) {
            if ($findLabels[$i]['status'] == "VOID") {
                $dataD['split_label_id'] = $findLabels[$i]['split_label_id'];
            }

            if($findLabels[$i]['status'] == "CREATED"){
                $dataD['label_id'] = $findLabels[$i]['id'];
                $this->updater->insertSplitLabelDetailDeatilApi($dataD, $user_id);
            }
        }

        $rtdata['message'] = "Get SplitLabelDetail Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $this->finder->findSplitLabelDetails($dataD);

        return $this->responder->withJson($response, $rtdata);

    }
}
