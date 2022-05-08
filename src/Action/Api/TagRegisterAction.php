<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class TagRegisterAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findPack;
    private $updatePack;
    private $findPacking;
    private $findCpoItem;
    private $updateCpoItem;

    public function __construct(Responder $responder,  TagUpdater $updater, TagFinder $finder, PackFinder $findPack, PackUpdater $updatePack, PackingFinder $findPacking, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findPack = $findPack;
        $this->updatePack = $updatePack;
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
        $user_id = $data['user_id'];

        $rtTags = $this->finder->findTags($data);

        if ($rtTags) {

            $packID = $rtTags[0]['pack_id'];

            $checkTagPrinted = true;
            for ($i = 0; $i < count($rtTags); $i++) {
                if ($rtTags[$i]['status'] != "PRINTED") {
                    $checkTagPrinted = false;
                }
            }

            if ($checkTagPrinted == true) {


                $rtPack = $this->findPack->findPackRow($packID);

                if ($rtPack['is_completed'] == 'Y') {

                    $rtPacking['PackingID'] =  $rtPack['packing_id'];

                    $sumQuantity = 0;
                    $rtPackingItem = $this->findPacking->findPackingItem($rtPacking);
                    for ($i = 0; $i < count($rtPackingItem); $i++) {
                        $sumQuantity += $rtPackingItem[$i]['Quantity'];
                    }

                    $rtSearch['cpo_item_id'] = $rtPackingItem[0]['CpoItemID'];
                    $rtCpoItem = $this->findCpoItem->findCpoItem($rtSearch);
                    $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty'];

                    $this->updateCpoItem->updateCpoItem((int)$rtPackingItem[0]['CpoItemID'], $isCpoItem);

                    $upStatus['status'] = "BOXED";
                    $this->updater->updateTagAllFromPackIDApi($packID, $upStatus,  $user_id);

                    $upStatus['up_status'] = "TAGGED";
                    $this->updatePack->updatePackStatus($packID, $upStatus, $user_id);

                    $rtdata['message'] = "Get Tag Successful";
                    $rtdata['error'] = false;
                    $rtdata['tags'] = $this->finder->findTags($data);
                } else if ($rtPack['is_completed'] == 'N') {

                    
                    $rtPacking['PackingID'] = $rtPack['packing_id'];

                    $sumQuantity = 0;
                    $rtPackingItem = $this->findPacking->findPackingItem($rtPacking);
                    for ($i = 0; $i < count($rtPackingItem); $i++) {
                        $sumQuantity += $rtPackingItem[$i]['Quantity'];
                    }

                    $rtSearch['cpo_item_id'] = $rtPackingItem[0]['CpoItemID'];
                    $rtCpoItem = $this->findCpoItem->findCpoItem($rtSearch);
                    $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty'];

                    $this->updateCpoItem->updateCpoItem((int)$rtPackingItem[0]['CpoItemID'], $isCpoItem);

                    $upStatus['status'] = "BOXED";
                    $this->updater->updateTagAllFromPackIDApi($packID, $upStatus,  $user_id);

                    $upStatus['up_status'] = "COMPLETE";
                    $this->updatePack->updatePackStatus($packID, $upStatus, $user_id);

                    $rtdata['message'] = "Get Tag Successful";
                    $rtdata['error'] = false;
                    $rtdata['tags'] = $this->finder->findTags($data);
                }
            } else {
                $rtdata['message'] = "Get Tag Successful";
                $rtdata['error'] = true;
                $rtdata['tag_no'] = $data['tag_no'];
            }
        } else {
            $rtdata['message'] = "Get Tag Successful";
            $rtdata['error'] = true;
            $rtdata['tag_no'] = $data['tag_no'];
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
