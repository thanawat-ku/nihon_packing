<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotFinder;
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

    public function __construct(
        Twig $twig,
        LotFinder $finder,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder,
        PrinterFinder $printerFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->printerFinder = $printerFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-7 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }
        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'lots' => $this->finder->findLots($params),
            'products' => $this->productFinder->findProducts($params),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/lots.twig', $viewData);
    }
}
