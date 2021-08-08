<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelDeleteAction
{
    private $responder;
    private $updater;
    public function __construct(Responder $responder, LabelUpdater $updater)
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
        $labelId = $data["id"];

        // Invoke the Domain with inputs and retain the result
        $this->updater->deleteLabel($labelId, $data);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"labels");
    }
}
