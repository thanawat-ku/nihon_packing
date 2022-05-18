<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SyncInvoiceNoAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findPack;
    private $updatePack;
    private $session;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        InvoiceFinder $finder,
        PackFinder $findPack,
        PackUpdater $updatePack,
        Responder $responder,
        Session $session
    ) {
        $this->finder = $finder;
        $this->findPack = $findPack;
        $this->updatePack = $updatePack;
        $this->responder = $responder;
        $this->session = $session;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getQueryParams();
        if (!isset($params['sync'])) {
            $params['sync'] = "true";
            $params['user_id'] = $this->session->get('user')["id"];
            $user_id = $params['user_id'];
        } else {
            $user_id = $params['user_id'];
        }

        $rtPack = $this->findPack->findPacks($params);
        $rtData = [];

        for ($i = 0; $i < count($rtPack); $i++) {
            $params['packing_id'] = $rtPack[$i]['packing_id'];
            $rtIvoice = $this->finder->findInvoice($params);

            if (isset($rtIvoice[0])) {
                $upPack['pack_status'] = 'INVOICED';
                $upPack['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
                $this->updatePack->updatePackSyncApi((int)$rtPack[$i]['id'], $upPack, $user_id);
            }
        }

        $rtData = [];
        array_push($rtData, $upPack);

        return $this->responder->withJson($response, $rtData);
    }
}
