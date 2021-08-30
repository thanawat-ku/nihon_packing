<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
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

    public function __construct(
        SplitLabelFinder $finder,
        Responder $responder,
        LabelUpdater $updaterLabel,
        SplitLabelUpdater  $updater,
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $labelID = $params['label_id'];
        $this->updater->insertSplitLabelApi($params, $user_id);

        $data = $this->finder->findSplitLabels($params);

        $params['split_label_no'] = "SP{$data[0]['id']}L{$data[0]['label_id']}";
        $splitID = $data[0]['id'];
        $params['status'] = "CREATED";
        $this->updater->updateSplitLabelApi($params, $splitID, $user_id);

        $dataLabel['split_label_id'] = $splitID;
        $dataLabel['status'] = "VOID";

        $this->updaterLabel->updateLabelApi($labelID, $dataLabel, $user_id);

        $rtdata['message'] = "Add Splitlabel Successful";
        $rtdata['error'] = false;
        $rtdata['split_labels'] = $this->finder->findSplitLabels($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
