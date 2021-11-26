<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotEditAction
{
    private $responder;
    private $updater;
    private $finder;

    public function __construct(Responder $responder, LotUpdater $updater, LotFinder $finder)
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
        $data = (array)$request->getParsedBody();
        $lotId = $data["id"];
        $findLot['lot_id'] = $lotId;
        $lot = $this->finder->findLots($findLot);

        if ($lot[0]['status'] == "CREATED") {
            $this->updater->updateLot($lotId, $data);
        }


        return $this->responder->withRedirect($response, "lots");
    }
}
