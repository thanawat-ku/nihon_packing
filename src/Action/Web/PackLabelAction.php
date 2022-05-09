<?php

namespace App\Action\Web;

use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use PHPUnit\Util\Printer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $packFinder;
    private $packLabelFinder;
    private $printer;
    private $session;

    public function __construct(
        Twig $twig,
        PackLabelFinder $finder,
        PackFinder $packFinder,
        PackLabelFinder $packLabelFinder,
        PackUpdater $updater,
        PrinterFinder $printer,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->packFinder = $packFinder;
        $this->packLabelFinder = $packLabelFinder;
        $this->printer = $printer;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $packID = (int)$params['pack_id'];

        $packLabels = [];

        $totalQty = 0;
        $arrtotalQty = [];

        $rtdata['mpd_from_lots'] = $this->finder->findPackLabelForlots($params);
        array_push($packLabels, $rtdata['mpd_from_lots']);
        if ($rtdata['mpd_from_lots']) {
            for ($i = 0; $i < count($packLabels[0]); $i++) {
                $totalQty += (int)$packLabels[0][$i]['quantity'];
            }
        }

        $rtdata['mpd_from_merges'] = $this->finder->findPackLabelForMergePacks($params);
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
        $packRow = $this->packFinder->findPackRow($packID);

        $packTotalQty = (int)$packRow['total_qty'];
        if ($totalQty < $packTotalQty) {
            $checkPackQty = "lesser";
        } else if ($totalQty == $packTotalQty) {
            $checkPackQty = "aqual";
        } else {
            $checkPackQty = "over";
        }

        $rtPackLabel = $this->packLabelFinder->findPackLabels($params);

        if (!$rtPackLabel) {
            $upStatus['pack_status'] = "SELECTED_CPO";
            $this->updater->updatePack($packID, $upStatus);
        }

        $params['printer_type'] = 'TAG';
        $rtPrinter = $this->printer->findPrinters($params);

        $viewData = [
            'checkPackQty' => $checkPackQty,
            'totalQtyLabelpack' => $arrtotalQty,
            'packRow' => $packRow,
            'printers' => $rtPrinter,
            'packLabels' => $packLabels,
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/packLabels.twig', $viewData);
    }
}
