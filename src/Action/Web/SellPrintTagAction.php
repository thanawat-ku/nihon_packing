<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellPrintTagAction
{
    private $responder;
    private $updater;
    private $updateTag;

    public function __construct(Responder $responder, SellUpdater $updater, TagUpdater $updateTag)
    {

        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateTag = $updateTag;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['sell_id'];

        $upStatus['status'] = "PRINTED";
        $this->updateTag->updateTagPrintFromSellID($sellID, $upStatus);
        $upStatus['sell_status'] = "PRINTED";
        $this->updater->updateSell($sellID, $upStatus);

        return $this->responder->withRedirect($response, "sells");
    }
}
