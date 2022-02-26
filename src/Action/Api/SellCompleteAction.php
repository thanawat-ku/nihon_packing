<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellCompleteAction
{
    private $responder;
    private $finder;
    private $updater;
    private $findPacking;
    private $findCpoItem;
    private $updateCpoItem;

    public function __construct(Responder $responder,SellFinder $finder,  SellUpdater $updater, PackingFinder $findPacking,CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findPacking = $findPacking;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $sellID = $data['sell_id'];
        $user_id = $data['user_id'];

        $rtSellRow = $this->finder->findSellRow($sellID);
        $rtPacking['PackingID'] = $rtSellRow['packing_id'];

        $sumQuantity = 0;
        $rtPackingItem = $this->findPacking->findPackingItem($rtPacking);
        for ($i=0; $i < count($rtPackingItem); $i++) { 
            $sumQuantity += $rtPackingItem[$i]['Quantity'];
        }
        
        $rtSearch['cpo_item_id'] = $rtPackingItem[0]['CpoItemID'];
        $rtCpoItem = $this->findCpoItem->findCpoItem($rtSearch);
        $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty']; 
       
        $this->updateCpoItem->updateCpoItem((int)$rtPackingItem[0]['CpoItemID'], $isCpoItem); 

        $data['sell_status'] = 'COMPLETE';
        $this->updater->updateSellStatus($sellID, $data, $user_id);

        return $this->responder->withJson($response, $data);
    }
}
