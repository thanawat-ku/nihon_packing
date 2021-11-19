<?php

namespace App\Action\Web;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellFinder;
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
final class SellLabelRemoveAction
{
    private $responder;
    private $twig;
    private $updater;
    private $labelUpdater;
    private $sellFinder;
    private $sellLabelFinder;
    private $session;

    public function __construct(
        Twig $twig,
        SellLabelUpdater $updater,
        Responder $responder,
        SellFinder $sellFinder,
        LabelUpdater $labelUpdater,
        Session $session,
        SellLabelFinder $sellLabelFinder,
    ) {
        $this->twig = $twig;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->sellFinder = $sellFinder;
        $this->session = $session;
        $this->sellLabelFinder = $sellLabelFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['sell_id'];
        $SelllbID = (int)$data['id'];

        $user_id = $this->session->get('user')["id"];

        $arrSellLabel = $this->sellLabelFinder->findSellLabels($data);

        if ($arrSellLabel) {
            $id = (int)$arrSellLabel[0]['label_id'];
            $dataUpdate['up_status'] = "PACKED";
            $this->labelUpdater->updateLabelStatus($id, $dataUpdate, $user_id);
        }

        $this->updater->deleteLabelInSellLabel($SelllbID);


        $sellRow = $this->sellFinder->findSellRow($sellID);

        $sellLabels = [];

        $totalQty = 0;
        $arrtotalQty = [];

        $rtdata['mpd_from_lots'] = $this->sellLabelFinder->findSellLabelForlots($data);
        array_push($sellLabels, $rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i = 0; $i < count($sellLabels[0]); $i++) {
                $totalQty += (int)$sellLabels[0][$i]['quantity'];
            }
        }

        $rtdata['mpd_from_merges'] = $this->sellLabelFinder->findSellLabelForMergePacks($data);
        array_push($sellLabels, $rtdata['mpd_from_merges']);
        if ($rtdata['mpd_from_merges'] != null) {
            for ($i = 0; $i < count($sellLabels[1]); $i++) {
                $totalQty += (int)$sellLabels[1][$i]['quantity'];
            }
        }
        if ($totalQty == 0) {
            $arrtotalQty = ["0"];
        } else {
            array_push($arrtotalQty, $totalQty);
        }

        $viewData = [
            'sell_id'=> $sellRow['id'],
            'product_id'=> $sellRow['product_id'],
        ];
        
        return $this->responder->withRedirect($response, "sell_labels",$viewData);
    }
}
