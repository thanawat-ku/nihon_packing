<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
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
    private $findcpo_item;


    public function __construct(Responder $responder,  SellUpdater $updater, SellFinder $finder, ProductFinder $findproduct, CpoItemFinder $findcpo_item)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findproduct = $findproduct;
        $this->findcpo_item = $findcpo_item;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $prodcutID = $data['ProductID'];

        $checkCreateSell = true;

        $rtSell = $this->finder->findSells($data);
        for ($i = 0; $i < count($rtSell); $i++) {
            if ($rtSell[$i]['product_id'] == $prodcutID && $rtSell[$i]['sell_status'] != "SELECTED_LABEL") {

                $checkCreateSell = ["Sell Not Empty"];
                $rtdata['message'] = "Get Lot Defect Successful";
                $rtdata['error'] = true;
                $rtdata['sells'] = $checkCreateSell;
                $checkCreateSell = false;
                break;
            }
        }

        if($checkCreateSell == true) {
            $id = $this->updater->insertSellApi($data, $user_id);
            $data['id'] = $id;
            $rtdata['message'] = "Get Lot Defect Successful";
            $rtdata['error'] = false;
            $rtdata['sells'] = $this->finder->findSells($data);
        }
        return $this->responder->withJson($response, $rtdata);
    }
}
