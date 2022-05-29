<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Printer\Service\PrinterFinder;
use App\Domain\Label\Service\LabelFinder;
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
    private $labelFinder;

    public function __construct(
        Twig $twig,
        LotFinder $finder,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder,
        PrinterFinder $printerFinder,
        LotDefectFinder $lotDefactFinder,
        LabelFinder $labelFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->printerFinder = $printerFinder;
        $this->lotDefactFinder = $lotDefactFinder;
        $this->labelFinder = $labelFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        
        if(isset($params['startDate'])){
            setcookie("startDateLot", $params['startDate'] , time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }
        else if(isset($_COOKIE['startDateLot'])){
            $params['startDate'] = $_COOKIE['startDateLot'];
            $params['endDate'] = $_COOKIE['endDateLot'];
        }
        else{
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateLot", $params['startDate'] , time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }

        if (isset($params['search_product_id']) || isset($params['search_status'])) {
            setcookie("search_product_id_lot", $params['search_product_id'], time() + 43200);
            setcookie("search_status_lot", $params['search_status'], time() + 43200);

        } else if (isset($_COOKIE['search_product_id_lot']) || isset($_COOKIE['search_status_lot'])) {
            $params['search_product_id'] = $_COOKIE['search_product_id_lot'] ?? 2713;
            $params['search_status'] = $_COOKIE['search_status_lot'] ?? 'CREATED';
        } else {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'CREATED';
            setcookie("search_product_id_lot", $params['search_product_id'], time() + 43200);
            setcookie("search_status_lot", $params['search_status'], time() + 43200);
        }

        $lots = $this->finder->findLotProduct($params);

        for ($i = 0; $i < sizeof($lots); $i++) {
            $lotID['lot_id'] = $lots[$i]['id'];
            $lotDefects = $this->lotDefactFinder->findLotDefectsPlusQty($lotID);
            if (isset($lotDefects[0])) {
                $qtyLotDefacts  = 0;
                foreach ($lotDefects as $lotDefect) {
                    $qtyLotDefacts = $qtyLotDefacts + (int)$lotDefect['quantity'];
                }
                $lots[$i]['qty_lot_defact'] = $qtyLotDefacts;
            } else {
                $lots[$i]['qty_lot_defact'] = 0;
            }

            if ($lots[$i]['status'] != "CREATED") {
                $labels = $this->labelFinder->findLabelSingleTable($lotID);
                $min = 99999;
                $max = -99999;

                foreach ($labels as $label) {
                    if ((int)$label['id'] > $max) {
                        $max = (int)$label['id'];
                        $maxLabel = $label['label_no'];
                    }
                    if ((int)$label['id'] < $min) {
                        $min = (int)$label['id'];
                        $minLabel = $label['label_no'];
                    }
                }
                $lots[$i]['max_label_no'] = $maxLabel;
                $lots[$i]['min_label_no'] = $minLabel;
            }
        }

        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'lots' => $lots,
            'products' => $this->productFinder->findProducts($params),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/lots.twig', $viewData);
    }
}
