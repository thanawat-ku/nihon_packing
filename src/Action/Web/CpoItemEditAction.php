<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Slim\Views\Twig;

use function DI\string;

/**
 * Action.
 */
final class CpoItemEditAction
{
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $updateSell;
    private $session;
    private $tempQueryFinder;

    public function __construct(Twig $twig, Responder $responder, SellFinder $finder,TempQueryFinder $tempQueryFinder, CpoItemUpdater $updater,SellUpdater $updateSell, Session $session)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateSell = $updateSell;
        $this->session=$session;
        $this->twig = $twig;
        $this->finder = $finder;
        $this->tempQueryFinder=$tempQueryFinder;
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
        
        $this->updater->updateCpoItem($id, $data);

        $rtSellCpoItem = $this->tempQueryFinder->findTempQuery($data);

        $totalQty = 0;
        for ($i=0; $i < count($rtSellCpoItem); $i++) { 
            $totalQty += $rtSellCpoItem[$i]['sell_qty'];
        }

        $rtSell['total_qty'] = $totalQty; 
        $this->updateSell->updateSell($sellID, $rtSell);
        $sellRow = $this->finder->findSellRow($sellID);
        
        $viewData = [
            'sellRow'=>$sellRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($data),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
