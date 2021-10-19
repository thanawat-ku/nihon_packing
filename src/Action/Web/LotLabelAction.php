<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
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


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        LotFinder $lotFinder,
        LabelVoidReasonFinder $voidReasonFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->lotFinder = $lotFinder;
        $this->voidReasonFinder = $voidReasonFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $lotId = $data["id"];
        $params["lot_id"] = $lotId;
        $lot =  $this->lotFinder->findLots($params);
        $labels = $this->finder->findLabels($params);

        if (isset($lot[0]['lot_no'])) {
            $lotNo = $lot[0]['generate_lot_no'];
        } else {
            $lotNo = "error";
        }

        $viewData = [
            'lot_no' => $lotNo,
            'labels' => $labels,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($data),
            'user_login' => $this->session->get('user'),
        ];
        return $this->twig->render($response, 'web/labelsLot.twig', $viewData);
    }
}
