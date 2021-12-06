<?php

namespace App\Action\Api;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class GetQtySellScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findermergepackdetail;
    private $findermergepack;

    public function __construct(
        SellLabelFinder $finder,
        MergePackDetailFinder $findermergepackdetail,
        Responder $responder,
        MergePackFinder $findermergepack
    ) {

        $this->finder = $finder;
        $this->findermergepackdetail = $findermergepackdetail;
        $this->findermergepack = $findermergepack;
        // $this->productFinder=$productFinder;

        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $productID = $params['product_id'];

        $rtdata['mpd_from_lots'] = $this->finder->findSellLabelForlots($params);
        $rtdata['mpd_from_merges'] = $this->finder->findSellLabelForMergePacks($params);

        $quantity = 0;
        $checklabelError = 0;
        $numberLabel = 0;

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) { //lot
            for ($i = 0; $i < count($rtdata['mpd_from_lots']); $i++) {
                $quantity+= $rtdata['mpd_from_lots'][$i]['quantity'];
                $numberLabel += 1;
                if ($rtdata['mpd_from_lots'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_lots'][$i]['status'] != "PACKED") && $rtdata['mpd_from_lots'][$i]['status'] != "SELLING") {
                    $checklabelError += 1;
                }
            }
            $arrayLabels = array($quantity, $checklabelError,$numberLabel);
        } else if ($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null) { //merge
            for ($i = 0; $i < count($rtdata['mpd_from_merges']); $i++) {
                $quantity += $rtdata['mpd_from_merges'][$i]['quantity'];
                $numberLabel += 1;
                if ($rtdata['mpd_from_merges'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_merges'][$i]['status'] != "PACKED") ) {
                    $checklabelError += 1;
                }
            }
            $arrayLabels = array($quantity, $checklabelError,$numberLabel);
        } else if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) { //lot and merge
            for ($i = 0; $i < count($rtdata['mpd_from_lots']); $i++) {
                $quantity += $rtdata['mpd_from_lots'][$i]['quantity'];
                $numberLabel += 1;
                if ($rtdata['mpd_from_lots'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_lots'][$i]['status'] != "PACKED") ) {
                    $checklabelError += 1;
                }
            }
            for ($i = 0; $i < count($rtdata['mpd_from_merges']); $i++) {
                $quantity += $rtdata['mpd_from_merges'][$i]['quantity'];
                $numberLabel += 1;
                if ($rtdata['mpd_from_merges'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_merges'][$i]['status'] != "PACKED") ) {
                    $checklabelError += 1;
                }
            }
            $arrayLabels = array($quantity, $checklabelError,$numberLabel);
        }
        
        $qtySellScan = $arrayLabels;
        return $this->responder->withJson($response, $qtySellScan);
    }
}
