<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


use function DI\string;

/**
 * Action.
 */
final class CpoItemEditAction
{
    private $cpoItemFinder;
    private $responder;
    private $finder;
    private $updater;
    private $updateSell;
    private $tempQueryFinder;
    private $tempQueryUpdater;
    private $sellCpoItemFinder;
    private $sellCpoItemUpdater;

    public function __construct(CpoItemFinder $cpoItemFinder, Responder $responder, SellFinder $finder, TempQueryFinder $tempQueryFinder,TempQueryUpdater $tempQueryUpdater, SellCpoItemFinder $sellCpoItemFinder,SellCpoItemUpdater $sellCpoItemUpdater, CpoItemUpdater $updater, SellUpdater $updateSell)
    {
        $this->cpoItemFinder = $cpoItemFinder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateSell = $updateSell;
        $this->finder = $finder;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->sellCpoItemFinder = $sellCpoItemFinder;
        $this->sellCpoItemUpdater = $sellCpoItemUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $sellID = (string)$data["sell_id"];
        $id = $data["id"];

        $this->sellCpoItemUpdater->updateSellCpoItemApi($id, $data);

        $rtSellCpoItem = $this->tempQueryFinder->findTempQuery($data);
        
        $rowSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

        $dataFinder['cpo_item_id'] = $rowSellCpoItem[0]['cpo_item_id'];
        $data['sell_qty'] = $rowSellCpoItem[0]['sell_qty'];
        $rtCpoItem = $this->cpoItemFinder->findCpoItem($dataFinder);
        $dataCpoItem['PackingQty'] = $data['sell_qty'];
        $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        $totalQty = 0;
        for ($i = 0; $i < count($rtSellCpoItem); $i++) {
            $totalQty += $rtSellCpoItem[$i]['sell_qty'];
        }

        $rtSell['total_qty'] = $totalQty;
        $this->updateSell->updateSell($sellID, $rtSell);

        // $dataCpoItem['packing_qty'] = $dataCpoItem['PackingQty'];
        // $this->tempQueryUpdater->updateTempquery($cpoItemID, $dataCpoItem);
        $this->updater->updateCpoItem($cpoItemID, $dataCpoItem);

        $sellRow = $this->finder->findSellRow($sellID);

        $viewData = [
            'sell_id' => $sellRow['id'],
            'product_id' => $sellRow['product_id'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
