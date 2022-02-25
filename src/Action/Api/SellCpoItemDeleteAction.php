<?php

namespace App\Action\Api;

use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\Sell\Service\SellUpdtaer;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class SellCpoItemDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findCpoItem;
    private $updateCpoItem;


    public function __construct(Responder $responder,  SellCpoItemUpdater $updater, SellCpoItemFinder $finder, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
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

        $rtSellCpoItem = $this->finder->findSellCpoItems($data);

        $dataFinder['cpo_item_id'] = $rtSellCpoItem[0]['cpo_item_id'];
        $data['sell_qty'] = $rtSellCpoItem[0]['sell_qty'];
        $rtCpoItem = $this->findCpoItem->findCpoItem($dataFinder);
        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $data['sell_qty'];
        $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        $this->updater->deleteSellCpoItemApi($sellCpoItemID);

        // $this->updateCpoItem->updateCpoItem($cpoItemID, $dataCpoItem);

        $rtdata['message'] = "Get Sell Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['sell_cpo_items'] = $this->finder->findSellCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
