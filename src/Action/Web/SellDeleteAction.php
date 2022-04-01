<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellDeleteAction
{
    private $finder;
    private $findSellLabel;
    private $responder;
    private $updater;
    private $updateLabel;
    private $cpoItemFinder;
    private $cpoItemUpdater;
    private $sellCpoItemFinder;
    private $sellLabelUpdater;
    private $sellCpoItemUpdater;
    private $tempQueryUpdater;
    private $packingUpdater;
    private $packingItemUpdater;

    public function __construct(Responder $responder, SellFinder $finder, SellLabelFinder $findSellLabel, SellLabelUpdater $sellLabelUpdater, SellUpdater $updater, LabelUpdater $updateLabel, CpoItemFinder $cpoItemFinder, CpoItemUpdater $cpoItemUpdater, SellCpoItemFinder $sellCpoItemFinder, SellCpoItemUpdater $sellCpoItemUpdater, TempQueryUpdater $tempQueryUpdater, PackingUpdater $packingUpdater, PackingItemUpdater $packingItemUpdater)
    {
        $this->finder = $finder;
        $this->findSellLabel = $findSellLabel;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateLabel = $updateLabel;
        $this->cpoItemFinder = $cpoItemFinder;
        $this->cpoItemUpdater = $cpoItemUpdater;
        $this->sellCpoItemFinder = $sellCpoItemFinder;
        $this->sellLabelUpdater = $sellLabelUpdater;
        $this->sellCpoItemUpdater = $sellCpoItemUpdater;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->packingUpdater = $packingUpdater;
        $this->packingItemUpdater = $packingItemUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['id'];


        $rtSell =  $this->finder->findSells($data);

        if ($rtSell[0]['sell_status'] != "COMPLETE") {

            
            if ($rtSell[0]['sell_status'] == "PRINTED") {
                $packingID = $rtSell[0]['packing_id'];
                $this->packingItemUpdater->deletePackingItemAll($packingID);
                $this->packingUpdater->deletePacking($packingID);
            } else if ($rtSell[0]['sell_status'] == "INVOICED" || $rtSell[0]['sell_status'] == "TAGGED") {
                $packingID = $rtSell[0]['packing_id'];
                $searchSellCpoItem['sell_id']=$sellID;
                $rowSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($searchSellCpoItem);
                $searchCpoItem['cpo_item_id'] = $rowSellCpoItem[0]['cpo_item_id'];
                $rowCpoItem = $this->cpoItemFinder->findCpoItem($searchCpoItem);

                $packingQtyOld = $rowCpoItem[0]['PackingQty']; 
                $packingQtyNew['PackingQty'] = $packingQtyOld - $rowSellCpoItem[0]['sell_qty'];
                
                $this->cpoItemUpdater->updateCpoItem((int)$rowSellCpoItem[0]['cpo_item_id'], $packingQtyNew); 

                $this->packingItemUpdater->deletePackingItemAll($packingID);
                $this->packingUpdater->deletePacking($packingID);
                
            }

            $rtSell['sell_id'] = $sellID;
            $rtSellLabel =  $this->findSellLabel->findSellLabels($rtSell);

            for ($i = 0; $i < count($rtSellLabel); $i++) {
                $upStatus['status'] = "PACKED";
                $this->updateLabel->updateLabel($rtSellLabel[$i]['label_id'], $upStatus);
            }

            $this->sellLabelUpdater->deleteSellLabelApi($sellID);
            $this->sellCpoItemUpdater->deleteCpoItemInSellCpoItemApi($sellID);

            $data['is_delete'] = 'Y';
            $this->updater->updateSell($sellID, $data);

            $this->tempQueryUpdater->deleteTempQuery((int)$rtSell[0]['product_id']);
        }

        return $this->responder->withRedirect($response, "sells");
    }
}
