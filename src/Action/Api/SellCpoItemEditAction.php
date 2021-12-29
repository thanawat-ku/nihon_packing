<?php

namespace App\Action\Api;

use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
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
   


    public function __construct(Responder $responder,  SellCpoItemUpdater $updater, SellCpoItemFinder $finder, SellUpdater $updatesell)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatesell=$updatesell;
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

        $this->updater->updateSellCpoItemApi($sellCpoItemID, $data, $user_id);
        $sells = $this->finder->findSellCpoItems($data);
        $this->updatesell->updateSellApi($sellID, $sells, $user_id);

        $rtdata['message'] = "Get Sell Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['sell_cpo_items'] = $this->finder->findSellCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
