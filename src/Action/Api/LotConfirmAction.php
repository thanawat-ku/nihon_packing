<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotConfirmAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $labelUpdater;

    public function __construct(LotFinder $finder, ProductFinder $productFinder, Responder $responder, LotUpdater $updater, LabelUpdater $labelUpdater)
    {
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $user_id = $params["user_id"];
        $lotID = $params["lot_id"];
        $dataLot["lot_id"] = $params["lot_id"];
        $realQTY = $params["Real_quantity"];

        $this->updater->updateLotApi($lotID, $params, $user_id);

        $getDatalot = $this->finder->findLots($dataLot);
        $stdPack = $getDatalot[0]['std_pack'];
        $num_pack = floor($realQTY / $stdPack);

        if($stdPack == 1){
            for ($i = 0; $i < $num_pack; $i++) {
                $data1['lot_id'] = $getDatalot[0]['id'];
                $data1['label_no'] = $getDatalot[0]['lot_no'] . str_pad($i, 3, "0", STR_PAD_LEFT);
                $data1['label_type'] = "FULLY";
                $data1['quantity'] = $stdPack;
                $data1['status'] = "CREATED";
                $this->labelUpdater->insertLabelApi($data1,$user_id);
            }
        }
        else{
            if ($num_pack  % 2 == 0) {
                for ($i = 0; $i < $num_pack; $i++) {
                    $data1['lot_id'] = $getDatalot[0]['id'];
                    $data1['label_no'] = $getDatalot[0]['lot_no'] . str_pad($i, 3, "0", STR_PAD_LEFT);
                    $data1['label_type'] = "FULLY";
                    $data1['quantity'] = $stdPack;
                    $data1['status'] = "CREATED";
                    $this->labelUpdater->insertLabelApi($data1,$user_id);
                }
            } else {
                for ($i = 0; $i < $num_pack; $i++) {
                    $data1['lot_id'] = $getDatalot[0]['id'];
                    $data1['label_no'] = $getDatalot[0]['lot_no'] . str_pad($i, 3, "0", STR_PAD_LEFT);
                    $data1['label_type'] = "FULLY";
                    $data1['quantity'] = $stdPack;
                    $data1['status'] = "CREATED";
                    $this->labelUpdater->insertLabelApi($data1,$user_id);
                }
                $data1['lot_id'] = $getDatalot[0]['id'];
                $data1['label_no'] = $getDatalot[0]['lot_no'] . str_pad($i, 3, "0", STR_PAD_LEFT);
                $data1['label_type'] = "NONFULLY";
                $data1['quantity'] = $stdPack;
                $data1['status'] = "CREATED";
                $this->labelUpdater->insertLabelApi($data1,$user_id);
            }
        }
        


        $rtdata['message'] = "Get EndLot Successful";
        $rtdata['error'] = false;
        $rtdata['lots'] = $this->finder->findLots($params);



        return $this->responder->withJson($response, $rtdata);
    }
}
