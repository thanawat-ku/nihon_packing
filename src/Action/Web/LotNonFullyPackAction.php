<?php

namespace App\Action\Web;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotNonFullyPackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $lNFPFinder;
    private $lotFinder;
    private $printerFinder;
    private $session;


    public function __construct(
        Twig $twig,
        Session $session,
        Responder $responder,
        LotNonFullyPackFinder $lNFPFinder,
        LotFinder $lotFinder,
        PrinterFinder $printerFinder,
    ) {
        $this->twig = $twig;
        $this->lNFPFinder = $lNFPFinder;
        $this->lotFinder = $lotFinder;
        $this->printerFinder = $printerFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();


        if (isset($params['startDate'])) {
            setcookie("startDateLot", $params['startDate'], time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        } else if (isset($_COOKIE['startDateLot'])) {
            $params['startDate'] = $_COOKIE['startDateLot'];
            $params['endDate'] = $_COOKIE['endDateLot'];
        } else {
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateLot", $params['startDate'], time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }

        $rtLNFPLot = $this->lNFPFinder->findLotNonFullyPacks($params);
        $searchData = $params;
        $searchData['search_prefer_lot_id'] = true;

        $rtLNFPPreferLot = $this->lNFPFinder->findLotNonFullyPacks($searchData);
        $lotRow = $this->lotFinder->findLotProduct($params);

        $rtLNFs = array_merge($rtLNFPLot, $rtLNFPPreferLot);
        $grouped_rtLNFPs = array();
        foreach ($rtLNFs as $rtLNF) {
            $grouped_rtLNFPs[$rtLNF['id']] = $rtLNF;
        }
        
        $lNFPQty = 0;
        $quantityItem = 0;
        foreach ($grouped_rtLNFPs as $grouped_rtLNFP) {
            $lNFPQty += $grouped_rtLNFP['quantity'];
            $quantityItem += 1;
        }
        $lotRow[0]['quantity_item'] = $quantityItem;
        $lotRow[0]['lNFPQty'] = $lNFPQty;

        $viewData = [
            'labels' => $grouped_rtLNFPs,
            'lot_id' => $params['lot_id'],
            'lotRow' => $lotRow[0],
            'printers' => $this->printerFinder->findPrinters($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/lotNonFullyPacks.twig', $viewData);
    }
}
