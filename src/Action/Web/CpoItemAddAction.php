<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $sellCpoItemUpdater;
    private $sellFinder;
    private $sellUpdater;
    private $session;
    private $tempQueryFinder;

    public function __construct(Twig $twig,CpoItemFinder $finder,SellCpoItemUpdater $sellCpoItemUpdater,
    Session $session,Responder $responder,SellUpdater  $sellUpdater, SellFinder $sellFinder,TempQueryFinder $tempQueryFinder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->sellFinder=$sellFinder;
        $this->sellFinder=$sellFinder;
        $this->sellUpdater=$sellUpdater;
        $this->sellCpoItemUpdater=$sellCpoItemUpdater;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $sell_id=(int)$data['sell_id'];

        
        $uuid=uniqid();
        $param_search['uuid']=$uuid;
        $param_search['sell_id']=$sell_id;

        $this->sellCpoItemUpdater->insertSellCpoItem($data);

        $flash = $this->session->getFlashBag();
        $flash->clear();

        $totalQty=0;

        $sellCpoItem = $this->tempQueryFinder->findTempQuery($param_search);

        
        for ($i=0; $i < count($sellCpoItem); $i++) { 
            $totalQty += (int)$sellCpoItem[$i]['sell_qty'];
            $arrTotalQty['total_qty']=$totalQty;
           
        }

        
        $this->sellUpdater->updateSellStatusSelectingCpo($sell_id,  $arrTotalQty);
    
        $sellRow = $this->sellFinder->findSellRow($sell_id);
        $data1['ProductID']=$sellRow['product_id']; 

        $arrTemQuery = $this->tempQueryFinder->findTempQuery($param_search);
        $arrCpoItem = $this->finder->findCpoItemSelect($data1);
        function CheckCpoItemSelect(array $arrTemQuery, array $arrCpoItem)
        {
            $arrCpoItemSelect = [];
            if ($arrTemQuery) {

                for ($i = 0; $i < count($arrCpoItem); $i++) {
                    $checkCpo = true;
                    for ($j = 0; $j < count($arrTemQuery); $j++) {
                        if ($arrCpoItem[$i]['CpoItemID'] == $arrTemQuery[$j]['cpo_item_id']) {
                            $checkCpo = false;
                        }
                    }
                    if ($checkCpo == true) {
                        array_push($arrCpoItemSelect, $arrCpoItem[$i]);
                    }
                }
            } else {
                $arrCpoItemSelect = $arrCpoItem;
            }
            if (!$arrCpoItemSelect) {
                $arrCpoItemSelect = [];
            }
            return $arrCpoItemSelect;
        }
       
        $viewData = [
            'sellRow'=>$sellRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($param_search),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
