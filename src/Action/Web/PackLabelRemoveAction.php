<?php

namespace App\Action\Web;

use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
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
final class PackLabelRemoveAction
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
        PackLabelUpdater $updater,
        Responder $responder,
        PackFinder $sellFinder,
        LabelUpdater $labelUpdater,
        Session $session,
        PackLabelFinder $sellLabelFinder
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
        $sellID = (int)$data['pack_id'];
        $PacklbID = (int)$data['id'];

        $user_id = $this->session->get('user')["id"];

        $arrPackLabel = $this->sellLabelFinder->findPackLabels($data);

        if ($arrPackLabel) {
            $id = (int)$arrPackLabel[0]['label_id'];
            $dataUpdate['up_status'] = "PACKED";
            $this->labelUpdater->updateLabelStatus($id, $dataUpdate, $user_id);
        }

        $this->updater->deleteLabelInPackLabel($PacklbID);


        $sellRow = $this->sellFinder->findPackRow($sellID);

        $packLabels = [];

        $totalQty = 0;
        $arrtotalQty = [];

        $rtdata['mpd_from_lots'] = $this->sellLabelFinder->findPackLabelForlots($data);
        array_push($packLabels, $rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i = 0; $i < count($packLabels[0]); $i++) {
                $totalQty += (int)$packLabels[0][$i]['quantity'];
            }
        }

        $rtdata['mpd_from_merges'] = $this->sellLabelFinder->findPackLabelForMergePacks($data);
        array_push($packLabels, $rtdata['mpd_from_merges']);
        if ($rtdata['mpd_from_merges'] != null) {
            for ($i = 0; $i < count($packLabels[1]); $i++) {
                $totalQty += (int)$packLabels[1][$i]['quantity'];
            }
        }
        if ($totalQty == 0) {
            $arrtotalQty = ["0"];
        } else {
            array_push($arrtotalQty, $totalQty);
        }

        $viewData = [
            'pack_id'=> $sellRow['id'],
            'product_id'=> $sellRow['product_id'],
        ];
        
        return $this->responder->withRedirect($response, "pack_labels",$viewData);
    }
}
