<?php

namespace App\Action\Web;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $sellFinder;
    private $session;

    public function __construct(Twig $twig,SellLabelFinder $finder,SellFinder $sellFinder,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->sellFinder=$sellFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $sellID=(int)$params['sell_id'];

        $sellLabels=[];

        $totalQty=0;
        $arrtotalQty=[];

        $rtdata['mpd_from_lots'] = $this->finder->findSellLabelForlots($params);
        array_push($sellLabels,$rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i=0; $i < count($sellLabels[0]); $i++) { 
                $totalQty += (int)$sellLabels[0][$i]['quantity'];
            }
        }
        
        $rtdata['mpd_from_merges'] = $this->finder->findSellLabelForMergePacks($params);
        array_push($sellLabels,$rtdata['mpd_from_merges']);
        if ($rtdata['mpd_from_merges'] != null) {
            for ($i=0; $i < count($sellLabels[1]); $i++) { 
                $totalQty += (int)$sellLabels[1][$i]['quantity'];
            }
        }
        if ($totalQty == 0) {
            $arrtotalQty=["0"];
        }else{
            array_push($arrtotalQty,$totalQty);
        }
       

        $sellRow = $this->sellFinder->findSellRow($sellID);

        $viewData = [
            'totalQtyLabelsell'=>$arrtotalQty,
            'sellRow'=>$sellRow,
            'sellLabels' => $sellLabels,
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/sellLabels.twig',$viewData);
    }
}
