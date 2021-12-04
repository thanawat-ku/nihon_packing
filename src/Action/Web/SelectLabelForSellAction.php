<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SelectLabelForSellAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $sellFinder;
    private $sellLabelFinder;


    public function __construct(Twig $twig, LabelFinder $finder, Session $session, Responder $responder, SellFinder $sellFinder, SellLabelFinder $sellLabelFinder)
    {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->sellFinder = $sellFinder;
        $this->sellLabelFinder = $sellLabelFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $sellID = (int)$data['sell_id'];
        $data['check_sell_label'] = true;

        $data['find_label_for_sell'] = "PACKED";

        $labels = [];
        $labelFromLot = $this->finder->findCreateMergeNoFromLabels($data);
        array_push($labels, $labelFromLot);
        $labelFromMerge = $this->finder->findLabelFromMergePacks($data);
        array_push($labels, $labelFromMerge);

        $totalLabelQty = 0;
        $sellQtyBalance = 0;
        $rtSellLabel = $this->sellLabelFinder->findSellLabels($data);
        for ($i = 0; $i < count($rtSellLabel); $i++) {
            $totalLabelQty += $rtSellLabel[$i]['quantity'];
        }

        $sellRow = $this->sellFinder->findSellRow($sellID);

        $sellQtyBalance =  $sellRow['total_qty'] - $totalLabelQty;
        if ($sellQtyBalance <= 0) {
            $sellQtyBalance = "Complete";
        }

        $viewData = [
            'sellQtyBalance' => $sellQtyBalance,
            'sellRow' => $sellRow,
            'labels' => $labels,
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/selectLabelForSells.twig', $viewData);
    }
}
