<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
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
    private $responder;
    private $finder;
    private $updateSell;
    private $tempQueryFinder;
    private $sellCpoItemUpdater;

    public function __construct( Responder $responder, SellFinder $finder, TempQueryFinder $tempQueryFinder,SellCpoItemUpdater $sellCpoItemUpdater, SellUpdater $updateSell)
    {
        $this->responder = $responder;
        $this->updateSell = $updateSell;
        $this->finder = $finder;
        $this->tempQueryFinder = $tempQueryFinder;
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

        $this->sellCpoItemUpdater->updateSellCpoItem($id, $data);

        $rtSellCpoItem = $this->tempQueryFinder->findTempQuery($data);
        

        $totalQty = 0;
        for ($i = 0; $i < count($rtSellCpoItem); $i++) {
            $totalQty += $rtSellCpoItem[$i]['sell_qty'];
        }

        $rtSell['total_qty'] = $totalQty;
        $this->updateSell->updateSell($sellID, $rtSell);


        $sellRow = $this->finder->findSellRow($sellID);

        $viewData = [
            'sell_id' => $sellRow['id'],
            'product_id' => $sellRow['product_id'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
