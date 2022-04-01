<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class SellDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findSellLabel;
    private $updateSellLabel;
    private $updateLabel;
    private $findSellCpoItem;
    private $updateSellCpoItem;
    private $findCpoItem;
    private $updateCpoItem;
    private $updateTempQuery;
    private $packingUpdater;
    private $packingItemUpdater;



    public function __construct(Responder $responder, SellFinder $finder,  SellUpdater $updater, SellLabelFinder $findSellLabel, SellLabelUpdater $updateSellLabel, LabelUpdater $updateLabel, SellCpoItemFinder $findSellCpoItem, SellCpoItemUpdater $updateSellCpoItem, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem, TempQueryUpdater $updateTempQuery, PackingUpdater $packingUpdater, PackingItemUpdater $packingItemUpdater)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findSellLabel = $findSellLabel;
        $this->updateSellLabel = $updateSellLabel;
        $this->updateLabel = $updateLabel;
        $this->findSellCpoItem = $findSellCpoItem;
        $this->updateSellCpoItem = $updateSellCpoItem;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
        $this->updateTempQuery = $updateTempQuery;
        $this->packingUpdater = $packingUpdater;
        $this->packingItemUpdater = $packingItemUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $sellID = $data['sell_id'];
        $user_id = $data['user_id'];

        $rtSell = $this->finder->findSells($data);
        if ($rtSell[0]['sell_status'] == "COMPLETE") {
            return $this->responder->withJson($response, null);
        } else {

            if ($rtSell[0]['sell_status'] == "PRINTED") {
                $packingID = $rtSell[0]['packing_id'];
                $this->packingItemUpdater->deletePackingItemAll($packingID);
                $this->packingUpdater->deletePacking($packingID);
            } else if ($rtSell[0]['sell_status'] == "INVOICED" || $rtSell[0]['sell_status'] == "TAGGED") {
                $packingID = $rtSell[0]['packing_id'];
                $searchSellCpoItem['sell_id']=$sellID;
                $rowSellCpoItem = $this->findSellCpoItem->findSellCpoItems($searchSellCpoItem);
                $searchCpoItem['cpo_item_id'] = $rowSellCpoItem[0]['cpo_item_id'];
                $rowCpoItem = $this->findCpoItem->findCpoItem($searchCpoItem);

                $packingQtyOld = $rowCpoItem[0]['PackingQty']; 
                $packingQtyNew['PackingQty'] = $packingQtyOld - $rowSellCpoItem[0]['sell_qty'];
                
                $this->updateCpoItem->updateCpoItem((int)$rowSellCpoItem[0]['cpo_item_id'], $packingQtyNew); 

                $this->packingItemUpdater->deletePackingItemAll($packingID);
                $this->packingUpdater->deletePacking($packingID);
                
            }
            
            $rtSellLabel = $this->findSellLabel->findSellLabels($data);
            $upStatus['status'] = "PACKED";
            for ($i = 0; $i < count($rtSellLabel); $i++) {
                $labelID = $rtSellLabel[$i]['label_id'];
                $this->updateLabel->updateLabelApi($labelID, $upStatus, $user_id);
            }

            $id['sell_id'] = $sellID;
            $rtSellCpoItem = $this->findSellCpoItem->findSellCpoItems($id);
            
            $data['cpo_item_id'] = $rtSellCpoItem[0]['cpo_item_id'];
            $rtCpoItem = $this->findCpoItem->findCpoItem($data);

            $packingQty['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $rtSellCpoItem[0]['sell_qty'];

            // $this->updateCpoItem->updateCpoItem((int)$data['cpo_item_id'], $packingQty);


            $this->updateSellLabel->deleteSellLabelApi($sellID);

            $this->updateSellCpoItem->deleteCpoItemInSellCpoItemApi($sellID);

            $data['is_delete'] = "Y";
            $this->updater->updateSellDeleteApi($sellID, $data, $user_id);

            $this->updateTempQuery->deleteTempQuery((int)$rtSell[0]['product_id']);

            return $this->responder->withJson($response, $data);
        }
    }
}
