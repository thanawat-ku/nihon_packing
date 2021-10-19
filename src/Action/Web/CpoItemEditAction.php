<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemUpdater;
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
    private $updater;
    public function __construct(Responder $responder, CpoItemUpdater $updater)
    {
        $this->responder = $responder;
        $this->updater = $updater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $sellID = (string)$data["sell_id"];
        $id = $data["id"];

        $sellRow = $this->sellFinder->findSellRow($sellID);
        $productID = (string)$sellRow['product_id'];
        
        $this->updater->updateCpoItem($id, $data);

        return $this->responder->withRedirect($response,"CpoItem?ProductID=$productID&sell_id=$sellID");
    }
}
