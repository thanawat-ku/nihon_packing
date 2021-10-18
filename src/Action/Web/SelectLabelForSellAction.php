<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SelectLabelForSellAction
{
   
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $sellFinder;

 
    public function __construct(Twig $twig,LabelFinder $finder,Session $session,Responder $responder, SellFinder $sellFinder)
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
        $sell_id=(int)$params['sell_id'];
        $params['check_sell_label']=true;

        $labels=[];
        $labelFromLot = $this->finder->findCreateMergeNoFromLabels($params);
        array_push($labels,$labelFromLot);
        $labelFromMerge = $this->finder->findLabelFromMergePacks($params);
        array_push($labels,$labelFromMerge);

        $sell = null;

        $sellRow = $this->sellFinder->findSellRow($sell_id);

        if($sellRow) {
            $sell = $sellRow;
        }

        $viewData = [
            'sellRow'=>$sell,
            'labels' => $labels,
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/selectLabelForSells.twig',$viewData);
    }
}
