<?php

namespace App\Action\Web;


use App\Domain\Sell\Service\SellFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellAddAction
{
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $responder;
    private $updater;
    private $session;

    public function __construct(Twig $twig, Responder $responder, SellFinder $finder, 
    SellUpdater $updater, TempQueryFinder $tempQueryFinder,Session $session)
    {
        $this->twig=$twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->session=$session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $id = $this->updater->insertSell($data);

        $rtSell['sell_id'] = $id;

        $sellRow = $this->finder->findSellRow($id);


        $param_search['sell_id'] = $id;

        $viewData = [
            'sell_id' => $sellRow['id'],
            'product_id' => $sellRow['product_id'],
         
        ];
        return $this->responder->withRedirect($response, "cpo_item_check_temp_query",$viewData);
        
    }
}
