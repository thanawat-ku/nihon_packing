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
    private $packFinder;
    private $packLabelFinder;


    public function __construct(Twig $twig, LabelFinder $finder, Session $session, Responder $responder, PackFinder $packFinder, PackLabelFinder $packLabelFinder)
    {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->packFinder = $packFinder;
        $this->packLabelFinder = $packLabelFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $packID = (int)$data['pack_id'];
        $data['check_pack_label'] = true;

        $data['find_label_for_pack'] = "PACKED";

        $labels = [];
        $labelFromLot = $this->finder->findCreateMergeNoFromLabels($data);
        array_push($labels, $labelFromLot);
        $labelFromMerge = $this->finder->findLabelFromMergePacks($data);
        array_push($labels, $labelFromMerge);

        $totalLabelQty = 0;
        $packQtyBalance = 0;
        $rtPackLabel = $this->packLabelFinder->findPackLabels($data);
        for ($i = 0; $i < count($rtPackLabel); $i++) {
            $totalLabelQty += $rtPackLabel[$i]['quantity'];
        }

        $packRow = $this->packFinder->findPackRow($packID);

        $packQtyBalance =  $packRow['total_qty'] - $totalLabelQty;
        if ($packQtyBalance <= 0) {
            $packQtyBalance = "Complete";
        }

        $viewData = [
            'packQtyBalance' => $packQtyBalance,
            'packRow' => $packRow,
            'labels' => $labels,
            'user_login' => $this->session->get('user'),
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->twig->render($response, 'web/selectLabelForPacks.twig', $viewData);
    }
}
