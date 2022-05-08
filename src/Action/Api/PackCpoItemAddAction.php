<?php

namespace App\Action\Api;

use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\Pack\Service\PackUpdtaer;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class PackCpoItemAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updatesell;
    private $findCpoItem;
    private $updateCpoItem;


    public function __construct(Responder $responder,  PackCpoItemUpdater $updater, PackCpoItemFinder $finder, CpoItemFinder $findCpoItem,CpoItemUpdater $updateCpoItem, PackUpdater $updatesell)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatesell=$updatesell;
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
        $sellID = $data['pack_id'];
        
        $rtCpoItem = $this->findCpoItem->findCpoItem($data);

        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] + $data['pack_qty'];
        $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        // $this->updateCpoItem->updateCpoItem($cpoItemID, $dataCpoItem);

        $this->updater->insertPackCpoItemApi($data, $user_id);

        $uptatus['pack_status'] = "SELECTING_CPO";
        $this->updatesell->updatePackStatus($sellID, $uptatus, $user_id);

        $rtdata['message'] = "Get Pack Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['pack_cpo_items'] = $this->finder->findPackCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
