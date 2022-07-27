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
        
        
        if(isset($params['startDate'])){
            setcookie("startDateLot", $params['startDate'] , time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }
        else if(isset($_COOKIE['startDateLot'])){
            $params['startDate'] = $_COOKIE['startDateLot'];
            $params['endDate'] = $_COOKIE['endDateLot'];
        }
        else{
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateLot", $params['startDate'] , time() + 3600);
            setcookie("endDateLot", $params['endDate'], time() + 3600);
        }


        $searchLabelP['search_label_non_fully']=true;
        $searchLabelP['ProductID']=$params['ProductID'];
        $rtLabels = $this->labelFinder->findLabels($searchLabelP); //find label
        $productRow = $this->productFinder->findProducts($params); //find product

        //find data of lot non fully pack for find quantity label
        $rtLNFPs = $this->lNFPFinder->findLotNonFullyPacks($params);

        $lNFPQty = 0;
        //sum quantity of labels
        for ($i=0; $i < count($rtLNFPs); $i++) { 
            $lNFPQty += $rtLNFPs[$i]['quantity'];
        }

        $printerType['printer_type'] = "LABEL";
        $viewData = [
            'labels' => $rtLabels,
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
