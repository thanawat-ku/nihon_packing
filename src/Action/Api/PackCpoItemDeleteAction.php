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
final class PackCpoItemDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findCpoItem;
    private $updateCpoItem;


    public function __construct(Responder $responder,  PackCpoItemUpdater $updater, PackCpoItemFinder $finder, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
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
        $sellCpoItemID = (int)$data['id'];

        $rtPackCpoItem = $this->finder->findPackCpoItems($data);

        $dataFinder['cpo_item_id'] = $rtPackCpoItem[0]['cpo_item_id'];
        $data['pack_qty'] = $rtPackCpoItem[0]['pack_qty'];
        $rtCpoItem = $this->findCpoItem->findCpoItem($dataFinder);
        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $data['pack_qty'];
        $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        $this->updater->deletePackCpoItemApi($sellCpoItemID);

        // $this->updateCpoItem->updateCpoItem($cpoItemID, $dataCpoItem);

        $rtdata['message'] = "Get Pack Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['pack_cpo_items'] = $this->finder->findPackCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
