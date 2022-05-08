<?php

namespace App\Action\Web;

use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackConfirmAction
{
    private $responder;
    private $updater;
    private $session;

    public function __construct(Responder $responder, PackUpdater $updater, Session $session)
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
        $sellID=(int)$data['pack_id'];
        $user_id=$this->session->get('user')["id"];

        $data['up_status'] = "SELECTED_CPO";
        $this->updater->updatePackStatus($sellID, $data,$user_id);

        return $this->responder->withRedirect($response,"packs");
    }
}
