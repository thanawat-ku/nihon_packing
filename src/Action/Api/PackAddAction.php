<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
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


    public function __construct(Responder $responder,  PackUpdater $updater, PackFinder $finder,
     ProductFinder $findproduct, CpoItemFinder $findCpoItem)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findproduct = $findproduct;
        $this->findCpoItem = $findCpoItem;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        if (isset($data['user_id']) && $data['product_id']) {
            $user_id = $data['user_id'];
            $prodcutID = $data['product_id'];

            $checkCreatePack = true;

            $rtPack = $this->finder->findPacks($data);
            for ($i = 0; $i < count($rtPack); $i++) {
                if ($rtPack[$i]['product_id'] == $prodcutID && ($rtPack[$i]['pack_status'] == "CREATED" || $rtPack[$i]['pack_status'] == "SLECTING_CPO" || $rtPack[$i]['pack_status'] == "SELECTED_CPO" || $rtPack[$i]['pack_status'] == "SELECTING_LABEL")) {
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

            }   
        }
        return $this->responder->withJson($response, $rtdata);
    }
}
