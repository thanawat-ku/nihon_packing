<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Printer\Service\PrinterFinder;

final class  LabelAction
{

    private $responder;
    private $twig;
    private $finder;
    private $voidReasonFinder;
    private $session;
    private $printerFinder;
    private $productFinder;

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        LabelVoidReasonFinder $voidReasonFinder,
        PrinterFinder $printerFinder,
        ProductFinder $productFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->voidReasonFinder = $voidReasonFinder;
        $this->printerFinder = $printerFinder;
        $this->productFinder = $productFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if(!isset($params['search_product_id'])){
            $params['search_product_id']=2713;
            $params['search_status']='CREATED';
        }

        
        $labels1 = $this->finder->findLabels($params);
        $data2['lot_zero'] = 0;
        $labels2 =  $this->finder->findLabelForLotZero($data2);
        if(isset($labels2[0])){
            $labelsAll = array_merge($labels1,$labels2);
        }
        else{
            $labelsAll = $labels1;
        }
        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'labels' => $labelsAll,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($params),
            'user_login' => $this->session->get('user'),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'products' => $this->productFinder->findProducts($params),
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],
        ];


        return $this->twig->render($response, 'web/labels.twig', $viewData);
    }
}
