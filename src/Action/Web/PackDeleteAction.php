<?php

namespace App\Action\Web;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
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

/**
 * Action.
 */
final class PackDeleteAction
{
    private $finder;
    private $findPackLabel;
    private $responder;
    private $updater;
    private $updateLabel;
    private $cpoItemFinder;
    private $cpoItemUpdater;
    private $packCpoItemFinder;
    private $packLabelUpdater;
    private $packCpoItemUpdater;
    private $tempQueryUpdater;
    private $packingItemFinder;
    private $packingItemUpdater;
    private $lotFinder;
    private $lotUpdater;
    private $tagUpdater;

    public function __construct(Responder $responder, PackFinder $finder, PackLabelFinder $findPackLabel, PackLabelUpdater $packLabelUpdater, PackUpdater $updater, LabelUpdater $updateLabel, CpoItemFinder $cpoItemFinder, CpoItemUpdater $cpoItemUpdater, PackCpoItemFinder $packCpoItemFinder, PackCpoItemUpdater $packCpoItemUpdater, TempQueryUpdater $tempQueryUpdater, PackingUpdater $packingUpdater, PackingFinder $packingItemFinder, PackingItemUpdater $packingItemUpdater, LotFinder $lotFinder, LotUpdater $lotUpdater, TagUpdater $tagUpdater)
    {
        $this->finder = $finder;
        $this->findPackLabel = $findPackLabel;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateLabel = $updateLabel;
        $this->cpoItemFinder = $cpoItemFinder;
        $this->cpoItemUpdater = $cpoItemUpdater;
        $this->packCpoItemFinder = $packCpoItemFinder;
        $this->packLabelUpdater = $packLabelUpdater;
        $this->packCpoItemUpdater = $packCpoItemUpdater;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->packingUpdater = $packingUpdater;
        $this->packingItemFinder = $packingItemFinder;
        $this->packingItemUpdater = $packingItemUpdater;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
        $this->tagUpdater = $tagUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $packID = (int)$data['id'];

        $gg = strlen($data['search_product_id']);
        $data['search_product_id'] = str_replace(' ', '', $data['search_product_id']);
        $gg = strlen($data['search_product_id']);

        $rtPack =  $this->finder->findPacks($data);


        if ($rtPack[0]['pack_status'] == "PRINTED" || $rtPack[0]['pack_status'] == "INVOICED" || $rtPack[0]['pack_status'] == "TAGGED" || $rtPack[0]['pack_status'] == "COMPLETED") {
            $packingID = $rtPack[0]['packing_id'];
            $searchPackCpoItem['pack_id'] = $packID;
            $rowPackCpoItem = $this->packCpoItemFinder->findPackCpoItems($searchPackCpoItem);
            $searchCpoItem['cpo_item_id'] = $rowPackCpoItem[0]['cpo_item_id'];
            $rowCpoItem = $this->cpoItemFinder->findCpoItem($searchCpoItem);

            $packingQtyOld = $rowCpoItem[0]['PackingQty'];
            $packingQtyNew['PackingQty'] = $packingQtyOld - $rowPackCpoItem[0]['pack_qty'];

            $this->cpoItemUpdater->updateCpoItem((int)$rowPackCpoItem[0]['cpo_item_id'], $packingQtyNew);

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

        $rtPack['pack_id'] = $packID;
        $rtPackLabel =  $this->findPackLabel->findPackLabels($rtPack);

        for ($i = 0; $i < count($rtPackLabel); $i++) {
            $upStatus['status'] = "PACKED";
            $this->updateLabel->updateLabel($rtPackLabel[$i]['label_id'], $upStatus);
        }

        $this->packLabelUpdater->deletePackLabelApi($packID);
        $this->packCpoItemUpdater->deleteCpoItemInPackCpoItemApi($packID);

        $data['is_delete'] = 'Y';
        $this->updater->updatePack($packID, $data);

        $this->tempQueryUpdater->deleteTempQuery((int)$rtPack[0]['product_id']);


        $viewData = [
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "packs", $viewData);
    }
}
