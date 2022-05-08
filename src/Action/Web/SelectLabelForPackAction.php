<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SelectLabelForPackAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $sellFinder;
    private $sellLabelFinder;


    public function __construct(Twig $twig, LabelFinder $finder, Session $session, Responder $responder, PackFinder $sellFinder, PackLabelFinder $sellLabelFinder)
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
        $sellID = (int)$data['pack_id'];
        $data['check_sell_label'] = true;

        $data['find_label_for_sell'] = "PACKED";

        $labels = [];
        $labelFromLot = $this->finder->findCreateMergeNoFromLabels($data);
        array_push($labels, $labelFromLot);
        $labelFromMerge = $this->finder->findLabelFromMergePacks($data);
        array_push($labels, $labelFromMerge);

        $totalLabelQty = 0;
        $sellQtyBalance = 0;
        $rtPackLabel = $this->sellLabelFinder->findPackLabels($data);
        for ($i = 0; $i < count($rtPackLabel); $i++) {
            $totalLabelQty += $rtPackLabel[$i]['quantity'];
        }

        $sellRow = $this->sellFinder->findPackRow($sellID);

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

        return $this->twig->render($response, 'web/selectLabelForPacks.twig', $viewData);
    }
}
