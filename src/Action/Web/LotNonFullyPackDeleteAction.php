<?php

namespace App\Action\Web;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class LotNonFullyPackDeleteAction
{
    private $responder;
    private $updatePack;
    private $labelUpdater;
    private $packFinder;
    private $session;
    private $lNFPUpdater;

    public function __construct(
        Session $session,
        Responder $responder,
        LotNonFullyPackUpdater $lNFPUpdater,
        LabelUpdater $labelUpdater
    ) {

        $this->lNFPUpdater = $lNFPUpdater;
        $this->labelUpdater = $labelUpdater;
        $this->responder = $responder;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        //update status label is MERGED
        $labelID=$data['id'];
        $updateLabel['up_status']='PACKED';
        $user_id = $this->session->get('user')["id"];
        $this->labelUpdater->updateLabelStatus($labelID, $updateLabel, $user_id);

        //delete label in tabel lot_non_fully_packs
        $this->lNFPUpdater->deleteLotNonFullyPack($labelID);

        $viewData = [
            'lot_id' => $data['lot_id'],
            'lot_id' => $data['lot_id'],
            'user_login' => $this->session->get('user'),
        ];

        return $this->responder->withRedirect($response, "lot_non_fully_packs", $viewData);
    }
}
