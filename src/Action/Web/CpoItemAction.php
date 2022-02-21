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
        $data = (array)$request->getQueryParams();
        $sellID=(int)$data['sell_id'];

        $cpodata = $this->finder->findCpoItem($data);
        $uuid=uniqid();

        $sell = null;

        $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($data);
        if (!$cpoitemcheck) {
            $checkSellCpo = "true";
        }else{
            $checkSellCpo = "false";
        }

        $sellRow = $this->sellFinder->findSellRow($sellID);

        $param_search['uuid']=$uuid;
        $param_search['sell_id']=$sellID;

       
      
        $viewData = [
            'checkSellCpo' => $checkSellCpo, 
            'sellRow'=>$sellRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($param_search),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
