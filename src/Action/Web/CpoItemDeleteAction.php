<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;



/**
 * Action.
 */
final class CpoItemDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updatePack;
    private $packCpoItemFinder;
    private $packCpoItemUpdater;
    private $cpoItemFinder;
    private $tempQueryUpdater;

    public function __construct(Responder $responder, TempQueryFinder $tempQueryFinder, CpoItemUpdater $updater, PackFinder $finder, PackUpdater $updatePack, PackCpoItemFinder $packCpoItemFinder, PackCpoItemUpdater $packCpoItemUpdater, CpoItemFinder $cpoItemFinder, TempQueryUpdater $tempQueryUpdater)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatePack = $updatePack;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->packCpoItemFinder = $packCpoItemFinder;
        $this->packCpoItemUpdater = $packCpoItemUpdater;
        $this->cpoItemFinder = $cpoItemFinder;
        $this->tempQueryUpdater = $tempQueryUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $packID = (string)$data["pack_id"];
        $id = $data["id"];

        $packRow = $this->finder->findPackRow($packID);

        $this->packCpoItemUpdater->deletePackCpoItemApi($id);

        $rtPackCpoItem = $this->packCpoItemFinder->findPackCpoItems($data);
        $totalQty = 0;
        for ($i = 0; $i < count($rtPackCpoItem); $i++) {
            $totalQty += $rtPackCpoItem[$i]['pack_qty'];
        }
        if ($totalQty == 0) {
            $dataPack['pack_status'] = "CREATED";
        }
        $dataPack['total_qty'] = $totalQty;
        $this->updatePack->updatePack($packID, $dataPack);

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
