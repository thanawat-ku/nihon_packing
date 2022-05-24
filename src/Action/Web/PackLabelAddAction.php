<?php

namespace App\Action\Web;

use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
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
final class PackLabelAddAction
{
    private $responder;
    private $twig;
    private $updater;
    private $finder;
    private $updatePack;
    private $labelUpdater;
    private $packFinder;
    private $session;

    public function __construct(
        Twig $twig,
        PackLabelFinder $finder,
        PackLabelUpdater $updater,
        PackUpdater $updatePack,
        Session $session,
        Responder $responder,
        PackFinder $packFinder,
        LabelUpdater $labelUpdater
    ) {

        $this->twig = $twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->packFinder = $packFinder;
        $this->session = $session;
        $this->updatePack = $updatePack;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $packID = (int)$data['pack_id'];
        $labelID = (int)$data['id'];

        $this->updater->insertPackLabel($data);
        $user_id = $this->session->get('user')["id"];
        $dataUpdate['up_status'] = "SELLING";
        $this->labelUpdater->updateLabelStatus($labelID, $dataUpdate, $user_id);

        $packLabels = [];

        $totalQty = 0;
        $arrtotalQty = [];

        $rtdata['mpd_from_lots'] = $this->finder->findPackLabelForlots($data);
        array_push($packLabels, $rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i = 0; $i < count($packLabels[0]); $i++) {
                $totalQty += (int)$packLabels[0][$i]['quantity'];
            }
        }

        $rtdata['mpd_from_merges'] = $this->finder->findPackLabelForMergePacks($data);
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

        $upstatus['pack_status'] = "SELECTING_LABEL";
        $this->updatePack->updatePack($packID, $upstatus);

        $packRow = $this->packFinder->findPackRow($packID);

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "pack_labels", $viewData);
    }
}
