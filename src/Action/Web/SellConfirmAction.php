<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellConfirmAction
{
    private $responder;
    private $updater;
    private $session;

    public function __construct(Responder $responder, SellUpdater $updater, Session $session)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->session=$session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID=(int)$data['sell_id'];
        $user_id=$this->session->get('user')["id"];

        $data['up_status'] = "SELECTED_CPO";
        $this->updater->updateSellStatus($sellID, $data,$user_id);

        return $this->responder->withRedirect($response,"sells");
    }
}
