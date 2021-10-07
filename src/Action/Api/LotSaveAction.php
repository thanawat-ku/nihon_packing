<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotSaveAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $labelUpdater;

    public function __construct(LotFinder $finder, ProductFinder $productFinder, Responder $responder, LotUpdater $updater, LabelUpdater $labelUpdater,)
    {
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        $lotID = $params["lot_id"];
        
        $this->updater->updateLotApi($lotID, $params, $user_id);

        $findlot['lot_id'] = $lotID;
        
        $rtdata['message'] = "Get EndLot Successful";
        $rtdata['error'] = false;
        $rtdata['lots'] = $this->finder->findLots($findlot);



        return $this->responder->withJson($response, $rtdata);
    }
}
