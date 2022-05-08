<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class PackAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findproduct;
    private $findCpoItem;
    private $updateTempQuery;
    private $findTempQuery;


    public function __construct(Responder $responder,  PackUpdater $updater, PackFinder $finder, ProductFinder $findproduct, CpoItemFinder $findCpoItem, TempQueryUpdater $updateTempQuery, TempQueryFinder $findTempQuery)
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

        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        }

        $prodcutID = $data['product_id'];

        $checkCreatePack = true;

        $rtPack = $this->finder->findPacks($data);
        for ($i = 0; $i < count($rtPack); $i++) {
            if ($rtPack[$i]['product_id'] == $prodcutID && ($rtPack[$i]['pack_status'] == "CREATED" || $rtPack[$i]['pack_status'] == "SLECTING_CPO" || $rtPack[$i]['pack_status'] == "SLECTED_CPO" || $rtPack[$i]['pack_status'] == "SLECTING_LABEL")) {
                $checkCreatePack = ["Pack Not Empty"];
                $rtdata['message'] = "Get Lot Defect Successful";
                $rtdata['error'] = true;
                $rtdata['packs'] = $checkCreatePack;
                $checkCreatePack = false;
                break;
            }
        }

        if ($checkCreatePack == true) {
            $id = $this->updater->insertPackApi($data, $user_id);
            $data['id'] = $id;
            $rtdata['message'] = "Get Lot Defect Successful";
            $rtdata['error'] = false;
            $rtdata['packs'] = $this->finder->findPacks($data);

            $params['pack_id'] = $id;
            $params['product_id'] = $prodcutID;
            $cpodata = $this->findCpoItem->findCpoItem($params);
            $uuid = uniqid();

            $cpoitemcheck = $this->findTempQuery->findTempQueryCheck($params);

            if (!$cpoitemcheck) {
                foreach ($cpodata as $cpo) {
                    $param_cpo['uuid'] = $uuid;
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
