<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Packing\Service\PackingUpdater;
use App\Domain\PackingItem\Service\PackingItemUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class PackDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findPackLabel;
    private $updatePackLabel;
    private $updateLabel;
    private $findPackCpoItem;
    private $updatePackCpoItem;
    private $findCpoItem;
    private $updateCpoItem;
    private $updateTempQuery;
    private $packingUpdater;
    private $packingItemUpdater;
    private $lotFinder;
    private $lotUpdater;
    private $packingItemFinder;
    private $tagUpdater;



    public function __construct(
        Responder $responder,
        PackFinder $finder,
        PackUpdater $updater,
        PackLabelFinder $findPackLabel,
        PackLabelUpdater $updatePackLabel,
        LabelUpdater $updateLabel,
        PackCpoItemFinder $findPackCpoItem,
        PackCpoItemUpdater $updatePackCpoItem,
        CpoItemFinder $findCpoItem,
        CpoItemUpdater $updateCpoItem,
        TempQueryUpdater $updateTempQuery,
        PackingUpdater $packingUpdater,
        PackingItemUpdater $packingItemUpdater,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater,
        PackingFinder $packingItemFinder,
        TagUpdater $tagUpdater
    ) {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findPackLabel = $findPackLabel;
        $this->updatePackLabel = $updatePackLabel;
        $this->updateLabel = $updateLabel;
        $this->findPackCpoItem = $findPackCpoItem;
        $this->updatePackCpoItem = $updatePackCpoItem;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
        $this->updateTempQuery = $updateTempQuery;
        $this->packingUpdater = $packingUpdater;
        $this->packingItemUpdater = $packingItemUpdater;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
        $this->packingItemFinder = $packingItemFinder;
        $this->tagUpdater = $tagUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $packID = $data['pack_id'];
        $user_id = $data['user_id'];

        $rtPack = $this->finder->findPacks($data);


        if ($rtPack[0]['pack_status'] == "PRINTED" || $rtPack[0]['pack_status'] == "INVOICED" || $rtPack[0]['pack_status'] == "TAGGED" || $rtPack[0]['pack_status'] == "COMPLETED") {
            $packingID = $rtPack[0]['packing_id'];
            $searchPackCpoItem['pack_id'] = $packID;
            $rowPackCpoItem = $this->findPackCpoItem->findPackCpoItems($searchPackCpoItem);
            $searchCpoItem['cpo_item_id'] = $rowPackCpoItem[0]['cpo_item_id'];
            $rowCpoItem = $this->findCpoItem->findCpoItem($searchCpoItem);

            $packingQtyOld = $rowCpoItem[0]['PackingQty'];
            $packingQtyNew['PackingQty'] = $packingQtyOld - $rowPackCpoItem[0]['pack_qty'];

            $this->updateCpoItem->updateCpoItem((int)$rowPackCpoItem[0]['cpo_item_id'], $packingQtyNew);

            //update cpo item return

            $searchPackingID['PackingID'] = $packingID;
            $rowPackingItem = $this->packingItemFinder->findPackingItem($searchPackingID);

            for ($i = 0; $i < count($rowPackingItem); $i++) {
                $searchLotID['LotID'] = $rowPackingItem[$i]['LotID'];
                $rowLot = $this->lotFinder->findLotNsps($searchLotID);
                $PackingQtyOld = $rowLot[0]['PackingQty'];
                $PackingQtyNew['PackingQty'] = $PackingQtyOld - $rowPackingItem[$i]['Quantity'];
                $this->lotUpdater->updateLotNsp((int)$rowPackingItem[$i]['LotID'], $PackingQtyNew);
            }

            //#############################################################

            $this->packingItemUpdater->deletePackingItemAll($packingID);

            $this->tagUpdater->deleteTags($packID);
        }

        $rtPackLabel = $this->findPackLabel->findPackLabels($data);
        $upStatus['status'] = "PACKED";
        for ($i = 0; $i < count($rtPackLabel); $i++) {
            $labelID = $rtPackLabel[$i]['label_id'];
            $this->updateLabel->updateLabelApi($labelID, $upStatus, $user_id);
        }

        $id['pack_id'] = $packID;
        $rtPackCpoItem = $this->findPackCpoItem->findPackCpoItems($id);

        $data['cpo_item_id'] = $rtPackCpoItem[0]['cpo_item_id'];
        $rtCpoItem = $this->findCpoItem->findCpoItem($data);

        $packingQty['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $rtPackCpoItem[0]['pack_qty'];

        // $this->updateCpoItem->updateCpoItem((int)$data['cpo_item_id'], $packingQty);


        $this->updatePackLabel->deletePackLabelApi($packID);

        $this->updatePackCpoItem->deleteCpoItemInPackCpoItemApi($packID);

        $data['is_delete'] = "Y";
        $this->updater->updatePackDeleteApi($packID, $data, $user_id);

        $this->updateTempQuery->deleteTempQuery((int)$rtPack[0]['product_id']);

        return $this->responder->withJson($response, $data);
    }
}
