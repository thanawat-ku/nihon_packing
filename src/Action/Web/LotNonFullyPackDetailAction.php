<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotNonFullyPackDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $labelFinder;
    private $productFinder;
    private $lNFPFinder;
    private $session;


    public function __construct(
        Twig $twig,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder,
        LabelFinder $labelFinder,
        LotNonFullyPackFinder $lNFPFinder,
    ) {
        $this->twig = $twig;
        $this->labelFinder = $labelFinder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->lNFPFinder = $lNFPFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();


        if (isset($params['startDate'])) {
            setcookie("startDateLot", $params['startDate'], time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        } else if (isset($_COOKIE['startDateLot'])) {
            $params['startDate'] = $_COOKIE['startDateLot'];
            $params['endDate'] = $_COOKIE['endDateLot'];
        } else {
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateLot", $params['startDate'], time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }


        $searchLabelP['search_label_non_fully'] = true;
        $searchLabelP['ProductID'] = $params['ProductID'];
        $rtLabelLotID = $this->labelFinder->findLabels($searchLabelP); //find label ที่เป็น NONFULLY
        $seachLabelRreferLotID['search_prefer_lot_id'] = true;
        $seachLabelRreferLotID['ProductID'] = $params['ProductID'];
        $rtLabelPreferLotID = $this->labelFinder->findLabels($seachLabelRreferLotID); //find label ที่เป็น MERGE_NONFULLY
        $productRow = $this->productFinder->findProducts($params); //find product

        $rtaLbels = array_merge($rtLabelLotID, $rtLabelPreferLotID);
        //เนื่องจากข้อมูลที่ได้มาซ้ำกัน จึงต้องจัดกลุ่ม
        $grouped_array = array();
        foreach ($rtaLbels as $rtaLbel) {
            $grouped_array[$rtaLbel['id']] = $rtaLbel;
        }
        // $rtaLbels[0]=$grouped_array;
        //find data of lot non fully pack for find quantity label
        $rtLNFPLots = $this->lNFPFinder->findLotNonFullyPacks($params);

        $serchLnfpMerge['search_prefer_lot_id'] = true;
        $serchLnfpMerge['prefer_lot_id'] = $params['lot_id'];
        $rtLNFPMerges = $this->lNFPFinder->findLotNonFullyPacks($serchLnfpMerge);


        $rtLNFPs = array_merge($rtLNFPLots, $rtLNFPMerges);
        //เนื่องจากข้อมูลที่ได้มาซ้ำกัน จึงต้องจัดกลุ่ม
        $grouped_rtLNFPs = array();
        foreach ($rtLNFPs as $rtLNFP) {
            $grouped_rtLNFPs[$rtLNFP['id']] = $rtLNFP;
        }

        $lNFPQty = 0;
        //sum quantity of labels
        // for ($i = 0; $i < count($grouped_rtLNFPs); $i++) {
        //     $lNFPQty += $grouped_rtLNFPs[$i]['quantity'];
        // }
        foreach ($grouped_rtLNFPs as $grouped_rtLNFP) {
            $lNFPQty += $grouped_rtLNFP['quantity'];
        }

        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'labels' => $grouped_array,
            'productRow' => $productRow[0],
            'lot_id' => $params['lot_id'],
            'lNFPQty' => $lNFPQty,
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/lotNonFullyPackDetails.twig', $viewData);
    }
}
