<?php

namespace App\Action\Api;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class SellDeleteAction
{
    private $responder;
    private $updater;
    private $findSellLabel;
    private $updateSellLabel;
    private $updateLabel;
    private $findSellCpoItem;
    private $updateSellCpoItem;


    public function __construct(Responder $responder,  SellUpdater $updater, SellLabelFinder $findSellLabel, SellLabelUpdater $updateSellLabel, LabelUpdater $updateLabel, SellCpoItemFinder $findSellCpoItem, SellCpoItemUpdater $updateSellCpoItem)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->findSellLabel = $findSellLabel;
        $this->updateSellLabel = $updateSellLabel;
        $this->updateLabel = $updateLabel;
        $this->findSellCpoItem = $findSellCpoItem;
        $this->updateSellCpoItem = $updateSellCpoItem;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $sellID = $data['sell_id'];
        $user_id = $data['user_id'];

        $data['is_delete'] = "Y";
        $this->updater->updateSellDeleteApi($sellID, $data, $user_id);

        $rtSellLabel = $this->findSellLabel->findSellLabels($data);
        $upStatus['status'] = "PACKED";
        for ($i = 0; $i < count($rtSellLabel); $i++) {
            $labelID = $rtSellLabel[$i]['label_id'];
            $this->updateLabel->updateLabelApi($labelID, $upStatus, $user_id);
        }

        $this->updateSellLabel->deleteSellLabelApi($sellID);

        $this->updateSellCpoItem->deleteCpoItemInSellCpoItemApi($sellID);

        return $this->responder->withJson($response, $data);
    }
}
