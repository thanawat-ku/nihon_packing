<?php

namespace App\Action\Api;

use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
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
    private $updatesell;
    private $findproduct;
    private $findcpo_item;


    public function __construct(Responder $responder,  SellCpoItemUpdater $updater, SellCpoItemFinder $finder, CpoItemFinder $findcpo_item, SellUpdater $updatesell)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findcpo_item = $findcpo_item;
        $this->updatesell=$updatesell;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $sellCpoItemID = (int)$data['id'];

        $this->updater->deleteSellCpoItemApi($sellCpoItemID);

        $rtdata['message'] = "Get Sell Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['sell_cpo_items'] = $this->finder->findSellCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
