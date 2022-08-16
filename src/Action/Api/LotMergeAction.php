<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotMergeAction
{
    private $finder;
    private $lnfpFinder;
    private $responder;

    public function __construct(
        LotNonFullyPackFinder $lnfpFinder,
        LotFinder $finder,
        Responder $responder,
    ) {
        $this->finder = $finder;
        $this->lnfpFinder = $lnfpFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];
        $lotID = $data['lot_id'];
         
        $rtLotMerge = [];
        $rtLots = $this->finder->findLotProduct($data);

        $searchData['search_prefer_lot_id'] = true;
        $searchData['prefer_lot_id'] = $lotID;
        $rtLnfp = $this->lnfpFinder->findLotNonFullyPacks($searchData);

        $actualLotQty = 0;
        $labelMergeQty = 0;
        $actualQty = 0;

        if ($rtLots[0]['quantity'] != 0) {
            $actualLotQty = $rtLots[0]['quantity'];
        }

        foreach ($rtLnfp as $lnfp) {
            $labelMergeQty += $lnfp['quantity'];
        }

        $actualQty = $actualLotQty + $labelMergeQty;

        $addData['lot_id'] = $lotID;
        $addData['actual_lot_qty'] = $actualLotQty;
        $addData['label_merge_qty'] =  $labelMergeQty;
        $addData['actual_qty'] = $actualQty;

        array_push($rtLotMerge,  $addData);

        $rtdata['message'] = "Get LotMerge Successful";
        $rtdata['error'] = false;
        $rtdata['lot_merges'] = $rtLotMerge;

        return $this->responder->withJson($response, $rtdata);
    }
}
