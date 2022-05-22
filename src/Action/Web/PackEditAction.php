<?php

namespace App\Action\Web;

use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackEditAction
{
    private $responder;
    private $updater;
    private $updateLabel;

    public function __construct(Responder $responder, PackUpdater $updater)
    {
       
        $this->responder = $responder;
        $this->updater = $updater;
        
        
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $packID = (int)$data['id'];

        $this->updater->updatePack($packID, $data);

        $viewData = [
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "packs", $viewData);
    }
}
