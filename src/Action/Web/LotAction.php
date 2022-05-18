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

        if(!isset($params['search_product_id'])){
            $params['search_product_id']=2713;
            $params['search_status']='CREATED';
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
        }

        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'lots' => $lots,
            'products' => $this->productFinder->findProducts($params),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],
        ];

        return $this->twig->render($response, 'web/lots.twig', $viewData);
    }
}
