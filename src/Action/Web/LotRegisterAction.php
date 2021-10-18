<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelUpdater;

/**
 * Action.
 */
final class LotRegisterAction
{
    private $responder;
    private $updater;
    private $finder;
    private $labelUpdater;
    public function __construct(
        Responder $responder,
        LotUpdater $updater,
        LotFinder $finder,
        LabelUpdater $labelUpdater
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->labelUpdater = $labelUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data['lot_id'];
        $dataPack['status'] = "PACKED";

        $this->labelUpdater->registerLabel($lotId, $dataPack);
        $this->updater->registerLot($lotId, $dataPack);

        // Build the HTTP response
        return $this->responder->withRedirect($response, "lots");
    }
}
