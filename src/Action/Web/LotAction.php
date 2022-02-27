<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $productFinder;
    private $session;
    private $printerFinder;
    private $lotDefactFinder;

    public function __construct(
        Twig $twig,
        LotFinder $finder,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder,
        PrinterFinder $printerFinder,
        LotDefectFinder $lotDefactFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->printerFinder = $printerFinder;
        $this->lotDefactFinder = $lotDefactFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if (!isset($params['startDate'])) {
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
        }
        $lots = $this->finder->findLots($params);

        for ($i = 0; $i < sizeof($lots); $i++) {
            $lotID['lot_id'] = $lots[$i]['id'];
            $lotDefects = $this->lotDefactFinder->findLotDefects($lotID);
            if (isset($lotDefects[0])) {
                $qtyLotDefacts  = 0;
                for ($j = 0; $j < sizeof($lotDefects); $j++) {
                    $qtyLotDefacts = $qtyLotDefacts + (int)$lotDefects[$j]['quantity'];
                }
                $lots[$i]['qty_lot_defact'] = $qtyLotDefacts;
            } else {
                $lots[$i]['qty_lot_defact'] = 0;
            }
        }

        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'lots' => $lots,
            'products' => $this->productFinder->findProducts($params),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/lots.twig', $viewData);
    }
}
