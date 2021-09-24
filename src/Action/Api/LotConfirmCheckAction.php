<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotConfirmCheckAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;


    public function __construct(LotFinder $finder, ProductFinder $productFinder, Responder $responder, LotUpdater $updater)
    {
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
       

        $findlot = $this->finder->findLots($params);

        if ($findlot[0]['status'] == "CREATED") {
            $rtdata['message'] = "Get EndLot Successful";
            $rtdata['error'] = false;
            $rtdata['lots'] = $this->finder->findLots($params);
        }
        else{
            $rtdata['message'] = "Get EndLot fail";
            $rtdata['error'] = true;
            $rtdata['lots'] = "error";
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
