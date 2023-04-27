<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class PackTagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findInvoice;
    private $twig;
    private $productFinder;
    private $session;


    public function __construct(
        Twig $twig,
        PackFinder $finder,
        ProductFinder $productFinder,
        InvoiceFinder $findInvoice,
        Session $session,
        Responder $responder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->findInvoice = $findInvoice;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        $totalTag = 0;
        $totalLabel = 0;

        $data['findPack'] = true;
        $rtInvoice = $this->findInvoice->findInvoicePackings($data);
        for ($i = 0; $i < count($rtInvoice); $i++) {
            $searchPackID['pack_id'] = $rtInvoice[$i]['pack_id'];

            $packTag = $this->finder->findPackTag($searchPackID);
            $packLabel = $this->finder->findPackLabel($searchPackID);

            $totalTag += count($packTag);
            $totalLabel += count($packLabel);
        }

        $invoiceRow['id'] = $data['invoice_id'];
        $invoiceRow['invoice_no'] = $rtInvoice[0]['invoice_no'];
        $invoiceRow['date'] = $rtInvoice[0]['date'];
        $invoiceRow['total_label'] = $totalLabel;
        $invoiceRow['total_tag'] = $totalTag;

        if (isset($invoiceRow['total_tag'])) {
            $rtdata['message'] = 'Login successfully';
            $rtdata['error'] = false;
            $rtdata['pack_row'] = $invoiceRow;
        } else {
            $rtdata['message'] = 'Login fail';
            $rtdata['error'] = true;
            $rtdata['pack_row'] = null;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
