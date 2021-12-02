<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelVoidAction
{
    private $responder;
    private $updater;
    private $finder;
    public function __construct(Responder $responder, LabelUpdater $updater, LabelFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();

        $labelId = $data["id"];
        $findLabel['label_id'] = $labelId;
        $label = $this->finder->findLabelSingleTable($findLabel);

        if ($label[0]['status'] == "PACKED") {
            $dataLabel['label_void_reason_id'] = $data['label_void_reason_id'];
            $dataLabel['status'] = "VOID";
            $this->updater->updateLabel($labelId, $dataLabel);
        }
        if ($data['from'] == "label_lot") {
            $lotId = $label[0]['lot_id'];
            $viewData = [
                'id' => $lotId,
            ];
            return $this->responder->withRedirect($response, "label_lot", $viewData);
        } else if ($data['from'] == "label_split") {
            $splitId = $data['split_id'];
            $viewData = [
                'id' => $splitId,
            ];
            return $this->responder->withRedirect($response, "label_splitlabel", $viewData);
        } else if ($data['from'] == "label_split") {
            $splitId = $data['split_id'];
            $viewData = [
                'id' => $splitId,
            ];
            return $this->responder->withRedirect($response, "label_splitlabel", $viewData);
        } else if ($data['from'] == "label_merge") {
            $mergeId = $data['merge_id'];
            $viewData = [
                'id' => $mergeId,
            ];
            return $this->responder->withRedirect($response, "merge_detail", $viewData);
        } else {
            return $this->responder->withRedirect($response, "labels");
        }
    }
}
