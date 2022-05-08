<?php

namespace App\Action\Api;

use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class PackCpoItemEditAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updatepack;
    private $updateCpoitem;
    private $findTempQuery;



    public function __construct(Responder $responder,  PackCpoItemUpdater $updater, PackCpoItemFinder $finder, PackUpdater $updatepack, CpoItemUpdater $updateCpoitem, TempQueryFinder $findTempQuery)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatepack = $updatepack;
        $this->updateCpoitem = $updateCpoitem;
        $this->findTempQuery = $findTempQuery;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $user_id = $data['user_id'];
        $packID = $data['pack_id'];
        $packCpoItemID = $data['id'];

        $rtTempQuery = $this->findTempQuery->findTempQuery($data);

        $rowPackCpoItem = $this->finder->findPackCpoItems($data);

        $dataFinder['cpo_item_id'] = $rowPackCpoItem[0]['cpo_item_id'];
        $dataCpoItem['PackingQty'] = $data['pack_qty'] + $rtTempQuery[0]['packing_qty'];
        $cpoItemID = $dataFinder['cpo_item_id'];

        $this->updater->updatePackCpoItemApi($packCpoItemID, $data, $user_id);
        $packs = $this->finder->findPackCpoItems($data);
        $this->updatepack->updatePackApi($packID, $packs, $user_id);

        // $this->updateCpoitem->updateCpoItem($cpoItemID, $dataCpoItem);

        $rtdata['message'] = "Get Pack Cpo Item Successful";
        $rtdata['error'] = false;
        $rtdata['pack_cpo_items'] = $this->finder->findPackCpoItems($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
