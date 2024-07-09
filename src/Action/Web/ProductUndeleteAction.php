<?php

namespace App\Action\Web;

use App\Domain\Product\Service\ProductUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ProductUndeleteAction
{
    private $responder;
    private $updater;
    public function __construct(Responder $responder, ProductUpdater $updater)
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
        $productId = $data["id"];

        // Invoke the Domain with inputs and retain the result
        $data2['is_delete'] = "N";
        $this->updater->updateProduct($productId,$data2);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"products");
    }
}
