<?php

namespace App\Action\Api;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotNonFullyPackRowAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $lotFinder;
    private $twig;
    private $session;

    public function __construct(
        Twig $twig,
        LotNonFullyPackFinder $finder,
        LotFinder $lotFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $params['search_prefer_lot_id'] = true;
        $params['prefer_lot_id'] = $params['lot_id'];

        $rtLabelMerge = $this->finder->findLotNonFullyPacks($params);
        $rtLot = $this->lotFinder->findLots($params);

        $mergeQty = 0;
        //ทำการหาจำนวน quantity ของ label
        for ($j = 0; $j < count($rtLabelMerge); $j++) {
            $mergeQty += $rtLabelMerge[$j]['quantity'];
        }

        $rtdata['message'] = "Get LotNonFullyPack Successful";
        $rtdata['error'] = false;
        $rtdata['lot_non_fully_pack_row'] = $rtLot;
        $rtdata['merge_qty'] = $mergeQty;

        return $this->responder->withJson($response, $rtdata);
    }
}
