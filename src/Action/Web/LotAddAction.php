<?php

namespace App\Action\Web;

use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotAddAction
{
    private $responder;
    private $updater;

    public function __construct(Responder $responder, LotUpdater $updater)
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

        // Invoke the Domain with inputs and retain the result
        $this->updater->insertLot( $data);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"lots");
    }
}
