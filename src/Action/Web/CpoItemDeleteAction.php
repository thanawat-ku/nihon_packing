<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;



/**
 * Action.
 */
final class CpoItemDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updateSell;
    private $sellCpoItemFinder;
    private $sellCpoItemUpdater;
    private $cpoItemFinder;

    public function __construct(Responder $responder, TempQueryFinder $tempQueryFinder, CpoItemUpdater $updater, SellFinder $finder, SellUpdater $updateSell, SellCpoItemFinder $sellCpoItemFinder, SellCpoItemUpdater $sellCpoItemUpdater, CpoItemFinder $cpoItemFinder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updateSell = $updateSell;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->sellCpoItemFinder = $sellCpoItemFinder;
        $this->sellCpoItemUpdater = $sellCpoItemUpdater;
        $this->cpoItemFinder = $cpoItemFinder;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (string)$data["sell_id"];
        $id = $data["id"];

        $sellRow = $this->finder->findSellRow($sellID);
        $rtSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);
        $dataFinder['cpo_item_id'] = $rtSellCpoItem[0]['cpo_item_id'];
        $data['sell_qty'] = $rtSellCpoItem[0]['sell_qty'];
        $rtCpoItem = $this->cpoItemFinder->findCpoItem($dataFinder);
        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $data['sell_qty'];
        $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        $this->sellCpoItemUpdater->deleteSellCpoItemApi($id);

        $rtSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);
        $totalQty = 0;
        for ($i = 0; $i < count($rtSellCpoItem); $i++) {
            $totalQty += $rtSellCpoItem[$i]['sell_qty'];
        }
        if ($totalQty == 0) {
            $dataSell['sell_status'] = "CREATED";
        }
        $dataSell['total_qty'] = $totalQty;
        $this->updateSell->updateSell($sellID, $dataSell);

        $this->updater->updateCpoItem($cpoItemID, $dataCpoItem);

        $viewData = [
            'sell_id' => $sellRow['id'],
            'product_id' => $sellRow['product_id'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
