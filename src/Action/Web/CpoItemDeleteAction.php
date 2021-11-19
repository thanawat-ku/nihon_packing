<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Slim\Views\Twig;


/**
 * Action.
 */
final class CpoItemDeleteAction
{
    private $twig;
    private $responder;
    private $updater;
    private $finder;
    private $updateSell;
    private $sellCpoItemFinder;
    private $tempQueryFinder;
    private $session;

    public function __construct(Twig $twig, Responder $responder,TempQueryFinder $tempQueryFinder, CpoItemUpdater $updater, SellFinder $finder,SellUpdater $updateSell,SellCpoItemFinder $sellCpoItemFinder, Session $session)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updateSell = $updateSell;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->sellCpoItemFinder=$sellCpoItemFinder;
        $this->session=$session;
        $this->twig = $twig;
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

        $this->updater->deleteCpoItem($id);

        $rtSellCpoItem = $this->sellCpoItemFinder->findSellCpoItems($data);

        $totalQty = 0;
        for ($i=0; $i < count($rtSellCpoItem); $i++) { 
            $totalQty += $rtSellCpoItem[$i]['sell_qty'];
        }
        if ($totalQty == 0) {
            $dataSell['sell_status']="CREATED";
        }
        $dataSell['total_qty']=$totalQty;
        $this->updateSell->updateSell($sellID, $dataSell);

       
        $viewData = [
            'sell_id'=> $sellRow['id'],
            'product_id'=> $sellRow['product_id'],
        ];
        
        return $this->responder->withRedirect($response, "cpo_items",$viewData);
    }
}
