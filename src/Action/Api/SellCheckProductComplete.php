<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class SellAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findproduct;
    private $findCpoItem;
    private $updateTempQuery;
    private $findTempQuery;


    public function __construct(Responder $responder,  SellUpdater $updater, SellFinder $finder, ProductFinder $findproduct, CpoItemFinder $findCpoItem, TempQueryUpdater $updateTempQuery, TempQueryFinder $findTempQuery)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findproduct = $findproduct;
        $this->findCpoItem = $findCpoItem;
        $this->findTempQuery = $findTempQuery;
        $this->updateTempQuery = $updateTempQuery;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $prodcutID = $data['product_id'];

        $checkCreateSell = true;

        $rtSell = $this->finder->findSells($data);
        for ($i = 0; $i < count($rtSell); $i++) {
            if ($rtSell[$i]['product_id'] == $prodcutID && ($rtSell[$i]['sell_status'] == "CREATED" || $rtSell[$i]['sell_status'] == "SLECTING_CPO" || $rtSell[$i]['sell_status'] == "SLECTED_CPO" || $rtSell[$i]['sell_status'] == "SLECTING_LABEL")) {
                $checkCreateSell = ["Sell Not Empty"];
                $rtdata['message'] = "Get Lot Defect Successful";
                $rtdata['error'] = true;
                $rtdata['sells'] = $checkCreateSell;
                $checkCreateSell = false;
                break;
            }
        }

        if ($checkCreateSell == true) {
            $id = $this->updater->insertSellApi($data, $user_id);
            $data['id'] = $id;
            $rtdata['message'] = "Get Lot Defect Successful";
            $rtdata['error'] = false;
            $rtdata['sells'] = $this->finder->findSells($data);

            $params['sell_id'] = $id;
            $params['product_id'] = $prodcutID;
            $cpodata = $this->findCpoItem->findCpoItem($params);
            $uuid = uniqid();

            $cpoitemcheck = $this->findTempQuery->findTempQueryCheck($params);

            if (!$cpoitemcheck) {
                foreach ($cpodata as $cpo) {
                    $param_cpo['uuid'] = $uuid;
                    $param_cpo['cpo_no'] = $cpo['CpoNo'];
                    $param_cpo['cpo_id'] = $cpo['CpoID'];
                    $param_cpo['cpo_item_id'] = $cpo['CpoItemID'];
                    $param_cpo['product_id'] = $cpo['ProductID'];
                    $param_cpo['quantity'] = $cpo['Quantity'];
                    $param_cpo['packing_qty'] = $cpo['PackingQty'];
                    $param_cpo['due_date'] = $cpo['DueDate'];
                    $this->updateTempQuery->insertTempQuery($param_cpo);
                }
            }
        }
        return $this->responder->withJson($response, $rtdata);
    }
}
