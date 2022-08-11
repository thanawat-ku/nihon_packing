<?php

namespace App\Action\Api;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotNonFullyPackDeleteAction
{
    private $responder;
    private $lnfpFinder;
    private $lnfpUpdater;
    private $labelFinder;
    private $labelUpdater;

    public function __construct(
        Responder $responder,
        LotNonFullyPackFinder $lnfpFinder,
        LotNonFullyPackUpdater $lnfpUpdater,
        LabelUpdater $labelUpdater,

    ) {
        $this->responder = $responder;
        $this->lnfpFinder = $lnfpFinder;
        $this->lnfpUpdater = $lnfpUpdater;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $labelID = $data['label_id'];
        $lotID = $data['lot_id'];
        $user_id = $data['user_id'];

        $this->lnfpUpdater->deleteLotNonFullyPack($labelID);
        $updateStatus['up_status'] = 'PACKED';
        $this->labelUpdater->updateLabelStatus($labelID, $updateStatus, $user_id);

        $rtdata['message'] = "Delete Lot Non Fully Pack Successful";
        $rtdata['error'] = false;
        // $rtdata['lot_non_fully_packs'] = $rtLabel;

        return $this->responder->withJson($response, $rtdata);
    }
}
