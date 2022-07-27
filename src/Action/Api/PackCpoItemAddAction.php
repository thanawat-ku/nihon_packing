<?php

namespace App\Action\Api;

use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Pack\Service\PackUpdtaer;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class PackCpoItemAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updatepack;
    private $findCpoItem;
    private $updateCpoItem;
    private $tempQueryFinder;


    public function __construct(Responder $responder,  PackCpoItemUpdater $updater, PackCpoItemFinder $finder, CpoItemFinder $findCpoItem,CpoItemUpdater $updateCpoItem, PackUpdater $updatepack,TempQueryFinder $tempQueryFinder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatepack=$updatepack;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
        $this->tempQueryFinder = $tempQueryFinder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $packID = $data['pack_id'];
        $param_search['pack_id'] = $packID;
        
        $rtCpoItem = $this->findCpoItem->findCpoItem($data);
       
        // $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] + $data['pack_qty'];
        // $cpoItemID = $rtCpoItem[0]['CpoItemID'];

        // $this->updateCpoItem->updateCpoItem($cpoItemID, $dataCpoItem);

        
        $this->updater->insertPackCpoItemApi($data, $user_id);


        $packCpoItem = $this->tempQueryFinder->findTempQuery($param_search);

        $totalQty = 0;

        for ($i = 0; $i < count($packCpoItem); $i++) {
            $totalQty += (int)$packCpoItem[$i]['pack_qty'];
            $arrTotalQty['total_qty'] = $totalQty;
            $arrTotalQty['po_no'] = $packCpoItem[$i]['po_no'];
        }

        $arrTotalQty['user_id']=$user_id;
        $this->updatepack->updatePackStatusSelectingCpo($packID,  $arrTotalQty);


        $uptatus['pack_status'] = "SELECTING_CPO";
        $this->updatepack->updatePackStatus($packID, $uptatus, $user_id);

        $rtdata['message'] = "Get Pack Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['pack_cpo_items'] = $this->finder->findPackCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
