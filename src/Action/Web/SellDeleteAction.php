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

    public function __construct(Responder $responder, SellFinder $finder, SellLabelFinder $findSellLabel, SellLabelUpdater $sellLabelUpdater, SellUpdater $updater, LabelUpdater $updateLabel, CpoItemFinder $cpoItemFinder, CpoItemUpdater $cpoItemUpdater, SellCpoItemFinder $sellCpoItemFinder, SellCpoItemUpdater $sellCpoItemUpdater, TempQueryUpdater $tempQueryUpdater)
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
        $this->tempQueryUpdater= $tempQueryUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['id'];


        $rtSell =  $this->finder->findSells($data);

        if ($rtSell[0]['sell_status'] == "CONFIRM" || $rtSell[0]['sell_status'] == "TAGGED" || $rtSell[0]['sell_status'] == "INVOICED") {
        } else {
            $rtSell['sell_id'] = $sellID;
            $rtSellLabel =  $this->findSellLabel->findSellLabels($rtSell);

            for ($i = 0; $i < count($rtSellLabel); $i++) {
                $upStatus['status'] = "PACKED";
                $this->updateLabel->updateLabel($rtSellLabel[$i]['label_id'], $upStatus);
            }

            $id['sell_id'] = $sellID;
            $rtSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($id);

            $data['cpo_item_id'] = $rtSellCpoItem[0]['cpo_item_id'];
            $rtCpoItem = $this->cpoItemFinder->findCpoItem($data);

            $packingQty['PackingQty'] = $rtCpoItem[0]['PackingQty'] - $rtSellCpoItem[0]['sell_qty'];

            // $this->cpoItemUpdater->updateCpoItem((int)$data['cpo_item_id'], $packingQty);

            $this->sellLabelUpdater->deleteSellLabelApi($sellID);
            $this->sellCpoItemUpdater->deleteCpoItemInSellCpoItemApi($sellID);

            $data['is_delete'] = 'Y';
            $this->updater->updateSell($sellID, $data);

            $this->tempQueryUpdater->deleteTempQuery((int)$rtSell[0]['product_id']);
        }

        return $this->responder->withRedirect($response, "sells");
    }
}
