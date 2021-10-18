<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellAddAction
{
    private $responder;
    private $updater;

    public function __construct(Responder $responder, SellUpdater $updater)
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

        $this->updater->insertSell($data);

        return $this->responder->withRedirect($response,"sells");
    }
}
