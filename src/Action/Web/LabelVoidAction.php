<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelVoidAction
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
        $dataLabel['label_void_reason_id'] = $data['label_void_reason_id'];
        $dataLabel['status'] = "VOID";
        $this->updater->updateLabel($labelId, $dataLabel);

        return $this->responder->withRedirect($response,"labels");
    }
}
