<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class PackEditAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findPackLabel;
    private $updatePackLabel;
    private $updateLabel;
    private $findPackCpoItem;
    private $updatePackCpoItem;


    public function __construct(Responder $responder, PackFinder $finder,  PackUpdater $updater, PackLabelFinder $findPackLabel, PackLabelUpdater $updatePackLabel, LabelUpdater $updateLabel, PackCpoItemFinder $findPackCpoItem, PackCpoItemUpdater $updatePackCpoItem)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findPackLabel = $findPackLabel;
        $this->updatePackLabel = $updatePackLabel;
        $this->updateLabel = $updateLabel;
        $this->findPackCpoItem = $findPackCpoItem;
        $this->updatePackCpoItem = $updatePackCpoItem;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $packID = $data['pack_id'];
        $user_id = $data['user_id'];

        $rtPack = $this->finder->findPacks($data);
        if ($rtPack[0]['pack_status'] == "CONFIRM" || $rtPack[0]['pack_status'] == "TAGGED" || $rtPack[0]['pack_status'] == "INVOICED") {
            return $this->responder->withJson($response, null);
        } else {
            $data['up_status'] = "SELECTING_CPO";
            $this->updater->updatePackStatus($packID, $data, $user_id);

            // $rtPackLabel = $this->findPackLabel->findPackLabels($data);
            // $upStatus['status'] = "PACKED";
            // for ($i = 0; $i < count($rtPackLabel); $i++) {
            //     $labelID = $rtPackLabel[$i]['label_id'];
            //     $this->updateLabel->updateLabelApi($labelID, $upStatus, $user_id);
            // }

            // $this->updatePackLabel->deletePackLabelApi($packID);

            // $this->updatePackCpoItem->deleteCpoItemInPackCpoItemApi($packID);

            return $this->responder->withJson($response, $data);
        }
    }
}
