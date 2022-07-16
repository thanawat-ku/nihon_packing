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

        if(isset($params['startDate'])){
            setcookie("startDateLabel", $params['startDate'] , time() + 3600);
            setcookie("endDateLabel", $params['endDate'], time() + 3600);
        }
        else if(isset($_COOKIE['startDateLabel'])){
            $params['startDate'] = $_COOKIE['startDateLabel'];
            $params['endDate'] = $_COOKIE['endDateLabel'];
        }
        else{
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateLabel", $params['startDate'] , time() + 3600);
            setcookie("endDateLabel", $params['endDate'], time() + 3600);
        }

        if (isset($params['search_product_id']) || isset($params['search_status'])) {
            setcookie("search_product_id_label", $params['search_product_id'], time() + 43200);
            setcookie("search_status_label", $params['search_status'], time() + 43200);

        } else if (isset($_COOKIE['search_product_id_label']) || isset($_COOKIE['search_status_label'])) {
            $params['search_product_id'] = $_COOKIE['search_product_id_label'] ?? 2713;
            $params['search_status'] = $_COOKIE['search_status_label'] ?? 'CREATED';
        } else {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'CREATED';
            setcookie("search_product_id_label", $params['search_product_id'], time() + 43200);
            setcookie("search_status_label", $params['search_status'], time() + 43200);
        }

        //cick link form tags screen 
        if (isset($params['find_label_tag'])) {
            $labels1 = $this->finder->findLabelFromTagLot($params);
            $labels2 =  $this->finder->findLabelFromTagMerge($params);
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
                'startDate' => $params['startDate'],
                'endDate' => $params['endDate'],
            ];    

            return $this->twig->render($response, 'web/labels.twig', $viewData);
        }

        
        $labels1 = $this->finder->findLabels($params);
        $labels2 =  $this->finder->findLabelForLotZero($params);
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
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];


        return $this->twig->render($response, 'web/labels.twig', $viewData);
    }
}
