<?php

namespace App\Action\Web;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class SellLabelAddAction
{
    private $responder;
    private $twig;
    private $updater;
    private $finder;
    private $updateSell;
    private $labelUpdater;
    private $sellFinder;
    private $session;

    public function __construct(
        Twig $twig,
        SellLabelFinder $finder,
        SellLabelUpdater $updater,
        SellUpdater $updateSell,
        Session $session,
        Responder $responder,
        SellFinder $sellFinder,
        LabelUpdater $labelUpdater,
    ) {
        
        $this->twig = $twig;
        $this->finder=$finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->sellFinder = $sellFinder;
        $this->session=$session;
        $this->updateSell=$updateSell;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['sell_id'];
        $labelID = (int)$data['id'];

        $upstatus['sell_status'] = "SELECTING_LABEL"; 
        $this->updateSell->updateSell($sellID,$upstatus);

        $this->updater->insertSellLabel($data);
        $user_id=$this->session->get('user')["id"];
        $dataUpdate['up_status']="SELLING";
        $this->labelUpdater->updateLabelStatus($labelID, $dataUpdate, $user_id);

        $sellLabels=[];

        $totalQty=0;
        $arrtotalQty=[];

        $rtdata['mpd_from_lots'] = $this->finder->findSellLabelForlots($data );
        array_push($sellLabels,$rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i=0; $i < count($sellLabels[0]); $i++) { 
                $totalQty += (int)$sellLabels[0][$i]['quantity'];
            }
        }
        
        $rtdata['mpd_from_merges'] = $this->finder->findSellLabelForMergePacks($data);
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

        
        $sellRow = $this->sellFinder->findSellRow($sellID);

        $viewData = [
            'sell_id'=> $sellRow['id'],
            'product_id'=> $sellRow['product_id'],
        ];
        
        return $this->responder->withRedirect($response, "sell_labels",$viewData);
    }
}
