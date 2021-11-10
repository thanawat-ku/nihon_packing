<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $sellFinder;
    private $tempQueryUpdater;
    private $session;

    public function __construct(Twig $twig,CpoItemFinder $finder,TempQueryFinder $tempQueryFinder,
    Session $session,Responder $responder,TempQueryUpdater  $tempQueryUpdater, SellFinder $sellFinder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->sellFinder=$sellFinder;
        $this->tempQueryUpdater=$tempQueryUpdater;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $sell_id=(int)$params['sell_id'];

        $cpodata = $this->finder->findCpoItem($params);
        $uuid=uniqid();

        $sell = null;

        $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($params);

        if(!$cpoitemcheck){
            foreach($cpodata as $cpo){
                $param_cpo['uuid']=$uuid;
                $param_cpo['cpo_no']=$cpo['CpoNo'];
                $param_cpo['cpo_id']=$cpo['CpoID'];
                $param_cpo['cpo_item_id']=$cpo['CpoItemID'];
                $param_cpo['product_id']=$cpo['ProductID'];
                $param_cpo['quantity']=$cpo['Quantity'];
                $param_cpo['packing_qty']=$cpo['PackingQty'];
                $param_cpo['due_date']=$cpo['DueDate'];
                $this->tempQueryUpdater->insertTempQuery($param_cpo);
            }
        }

        $sellRow = $this->sellFinder->findSellRow($sell_id);

        if($sellRow) {
            $sell = $sellRow;
        }

        $param_search['uuid']=$uuid;
        $param_search['sell_id']=$sell_id;
      
        $viewData = [
            'sellRow'=>$sell,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($param_search),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}