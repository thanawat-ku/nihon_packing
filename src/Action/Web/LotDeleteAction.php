<?php

namespace App\Action\Web;

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
    public function __construct(Responder $responder, LotUpdater $updater , LabelUpdater $updaterLabel,LotDefectUpdater $updaterLotDefect ,)
    {
        $this->responder = $responder;
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


        $this->updaterLabel->deleteLabelAll($lotId , $data);

        // Invoke the Domain with inputs and retain the result

        $this->updater->deleteLot($lotId, $data);
        
        $this->updaterLotDefect->deleteLotDefectAll($lotId);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"lots");
    }
}
