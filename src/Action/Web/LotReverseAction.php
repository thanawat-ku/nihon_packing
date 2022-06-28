<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotReverseAction
{
    private $responder;
    private $updater;
    private $lotUpdater;
    private $finder;

    public function __construct(Responder $responder, LabelUpdater $updater, LotUpdater $lotUpdater, LabelFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->lotUpdater = $lotUpdater;
        $this->finder = $finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $lotId = $data["lot_id"];
        $params['lot_id']=$lotId;
        $params['lot_id']=$lotId;

        $labels = $this->finder->findLabels($params);
        for ($i = 0; $i < sizeof($labels); $i++) {
            $labelId = $labels[$i]['id'];
            $labelData['is_delete'] = "Y";
            $labelData['status'] = "VOID";
            $this->updater->updateLabel($labelId, $labelData);
        }
        $lotData['generate_lot_no'] = "";
        $lotData['status'] = "CREATED";
        $this->lotUpdater->updateLot($lotId, $lotData);
        return $this->responder->withRedirect($response, "lots");
    }
}
