<?php

namespace App\Action\Web;

use App\Domain\Product\Service\ProductUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ProductDeleteAction
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
        $this->updater->deleteProduct($productId, $data);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"products");
    }
}
