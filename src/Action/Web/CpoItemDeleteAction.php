<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemUpdater;
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
        $cpoItemID = $data["id"];
        
        $this->updater->deleteCpoItem($cpoItemID);

        return $this->responder->withRedirect($response,"cpoItem");
    }
}
