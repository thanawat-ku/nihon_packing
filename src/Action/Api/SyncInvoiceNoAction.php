<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
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
    private $findSell;
    private $updateSell;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        InvoiceFinder $finder,
        SellFinder $findSell,
        SellUpdater $updateSell,
        Responder $responder,
        Session $session
    ) {
        $this->finder = $finder;
        $this->findSell = $findSell;
        $this->updateSell = $updateSell;
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
        $user_id = $params['user_id'];

        $rtSell = $this->findSell->findSells($params);

        for ($i = 0; $i < count($rtSell); $i++) {
            $params['packing_id'] = $rtSell[$i]['packing_id'];
            $rtIvoice = $this->finder->findInvoice($params);

            $upSell['sell_status'] = 'INVOICED';
            $upSell['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
            $this->updateSell->updateSellSyncApi((int)$rtSell[$i]['id'], $upSell, $user_id);
        }


        return $this->responder->withJson($response, $params);
    }
}
