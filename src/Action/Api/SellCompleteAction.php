<?php

namespace App\Action\Api;


use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellCompleteAction
{
    private $responder;
    private $updater;

    public function __construct(Responder $responder,  SellUpdater $updater)
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
        $sellID = $data['sell_id'];
        $user_id = $data['user_id'];

        $data['sell_status'] = 'COMPLETE';
        $this->updater->updateSellStatus($sellID, $data, $user_id);


        return $this->responder->withJson($response, $data);
    }
}
