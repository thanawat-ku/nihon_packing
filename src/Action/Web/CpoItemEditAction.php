<?php

namespace App\Action\Web;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


use function DI\string;

/**
 * Action.
 */
final class CpoItemEditAction
{
    private $responder;
    private $finder;
    private $updatePack;
    private $tempQueryFinder;
    private $packCpoItemUpdater;

    public function __construct( Responder $responder, PackFinder $finder,PackCpoItemUpdater $packCpoItemUpdater, PackUpdater $updatePack)
    {
        $this->responder = $responder;
        $this->updatePack = $updatePack;
        $this->finder = $finder;
        $this->packCpoItemUpdater = $packCpoItemUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $packID = (string)$data["pack_id"];
        $id = $data["id"];

        $this->packCpoItemUpdater->updatePackCpoItem($id, $data);

        $rtPack['total_qty'] = $data['pack_qty'];
        $this->updatePack->updatePack($packID, $rtPack);


        $packRow = $this->finder->findPackRow($packID);

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
