<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Invoice\Service\InvoiceUpdater;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackCompleteAction
{
    private $responder;
    private $finder;
    private $updater;
    private $fincdInvoice;
    private $updateInvoice;
    private $updateTag;

    public function __construct(Responder $responder, PackFinder $finder,  PackUpdater $updater, InvoiceFinder $fincdInvoice, InvoiceUpdater $updateInvoice, TagUpdater $updateTag)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->fincdInvoice = $fincdInvoice;
        $this->updateInvoice = $updateInvoice;
        $this->updateTag = $updateTag;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $invoiceID = $data['invoice_id'];
        $user_id = $data['user_id'];

        $data['findPack'] = true;
        $rtInvoice = $this->fincdInvoice->findInvoicePackings($data);

        $data['pack_status'] = 'COMPLETE';
        $upstatusTag['status'] = 'COMPLETE';
        for ($i = 0; $i < count($rtInvoice); $i++) {
            $packID = $rtInvoice[$i]['pack_id'];
            $this->updateTag->updateTagPrintFromPackIDApi($packID, $upstatusTag, $user_id);
            $this->updater->updatePackStatus($packID, $data, $user_id);
        }

        $upIvoiceStatus['invoice_status'] = 'COMPLETED';
        $this->updateInvoice->updateInvoicePacking($invoiceID, $upIvoiceStatus, $user_id);

        return $this->responder->withJson($response, $data);
    }
}
