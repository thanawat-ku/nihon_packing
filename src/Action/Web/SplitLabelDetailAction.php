<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SplitLabelDetailAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $splitDetailFinder;
    private $labelFinder;
    private $printerFinder;
    private $voidReasonFinder;

    public function __construct(
        Twig $twig,
        SplitLabelFinder $finder,
        LabelFinder $labelFinder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        LabelVoidReasonFinder $voidReasonFinder,
        PrinterFinder $printerFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->labelFinder = $labelFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->voidReasonFinder = $voidReasonFinder;
        $this->printerFinder = $printerFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $SplitLabelId = $data["id"];

        $dataDetail["split_label_id"] = $SplitLabelId;
        $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($dataDetail);

        $labels = [];
        for ($i = 0; $i < sizeof($labelDetail); $i++) {

            $labelId['label_id'] = $labelDetail[$i]['label_id'];
            $label = $this->labelFinder->findLabels($labelId);
            if (!isset($label[0])) {
                $label = $this->labelFinder->findLabelForLotZero($labelId);
            }
            array_push($labels, $label[0]);
        }
        $findSplitNo['split_label_id'] = $SplitLabelId;
        $splitLabel = $this->finder->findSplitLabels($findSplitNo);

        if (isset($splitLabel[0])) {
            $split =  $splitLabel[0];
            if ($split['status'] == "PRINTED") {
                $split['register'] = "Y";
            } else {
                $split['register'] = "N";
            }
        } else {
            $split = "ERROR";
        }
        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'split' => $split,
            'labels' => $labels,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($data),
            'printers' => $this->printerFinder->findPrinters($printerType),
            'user_login' => $this->session->get('user'),
        ];
        return $this->twig->render($response, 'web/labelsSplit.twig', $viewData);
    }
}
