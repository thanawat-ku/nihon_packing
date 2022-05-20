<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  LotLabelAction
{

    private $responder;
    private $twig;
    private $finder;
    private $lotFinder;
    private $session;
    private $printerFinder;

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        LotFinder $lotFinder,
        LabelVoidReasonFinder $voidReasonFinder,
        PrinterFinder $printerFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->lotFinder = $lotFinder;
        $this->voidReasonFinder = $voidReasonFinder;
        $this->printerFinder = $printerFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $lotId = $data["id"];
        $params["lot_id"] = $lotId;
        $lots =  $this->lotFinder->findLots($params);

        if (isset($lots[0]['lot_no'])) {
            $labels = $this->finder->findLabels($params);
            $lot = $lots[0];
            if ($lot['status'] == "PRINTED") {
                $lot['register'] = "Y";
            } else {
                $lot['register'] = "N";
            }
        } else {
            $lot = "error";
        }
        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'lot' => $lot,
            'labels' => $labels,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($data),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
            'search_product_id' => $data['search_product_id'],
            'search_status' => $data['search_status'],
        ];
        return $this->twig->render($response, 'web/labelsLot.twig', $viewData);
    }
}
