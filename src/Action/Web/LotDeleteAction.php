<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotDefect\Service\LotDefectUpdater;

/**
 * Action.
 */
final class LotDeleteAction
{
    private $responder;
    private $updater;
    private $updaterLabel;
    private $updaterLotDefect;
    private $finder;
    public function __construct(Responder $responder, LotUpdater $updater, LabelUpdater $updaterLabel, LotDefectUpdater $updaterLotDefect, LotFinder $finder)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updaterLabel = $updaterLabel;
        $this->updaterLotDefect = $updaterLotDefect;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data["id"];
        $findLot['lot_id'] = $lotId;
        $lot = $this->finder->findLots($findLot);

        if ($lot[0]['status'] == "CREATED") {
            $this->updater->IsdeleteLot($lotId, $data);
            // $this->updaterLabel->deleteLabelAll($lotId , $data);
            // $this->updaterLotDefect->deleteLotDefectAll($lotId);
        }


        return $this->responder->withRedirect($response, "lots");
    }
}
