<?php

namespace App\Action\Api;

use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class SellCpoItemEditAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updatesell;
    private $updateCpoitem;
    private $findTempQuery;



    public function __construct(Responder $responder,  SellCpoItemUpdater $updater, SellCpoItemFinder $finder, SellUpdater $updatesell, CpoItemUpdater $updateCpoitem, TempQueryFinder $findTempQuery)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatesell = $updatesell;
        $this->updateCpoitem = $updateCpoitem;
        $this->findTempQuery = $findTempQuery;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $sellID = $data['sell_id'];
        $sellCpoItemID = $data['id'];

        $rtTempQuery = $this->findTempQuery->findTempQuery($data);

        $rowSellCpoItem = $this->finder->findSellCpoItems($data);

        $dataFinder['cpo_item_id'] = $rowSellCpoItem[0]['cpo_item_id'];
        $dataCpoItem['PackingQty'] = $data['sell_qty'] + $rtTempQuery[0]['packing_qty'];
        $cpoItemID = $dataFinder['cpo_item_id'];

        $this->updater->updateSellCpoItemApi($sellCpoItemID, $data, $user_id);
        $sells = $this->finder->findSellCpoItems($data);
        $this->updatesell->updateSellApi($sellID, $sells, $user_id);

        $this->updateCpoitem->updateCpoItem($cpoItemID, $dataCpoItem);

        $rtdata['message'] = "Get Sell Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['sell_cpo_items'] = $this->finder->findSellCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
