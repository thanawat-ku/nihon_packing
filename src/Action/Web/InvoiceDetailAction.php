<?php

namespace App\Action\Web;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Customer\Service\CustomerFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class InvoiceDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $invoiceFinder;
    private $customerFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig, InvoiceFinder $invoiceFinder, CustomerFinder $customerFinder, Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->invoiceFinder=$invoiceFinder;
        $this->customerFinder=$customerFinder;
        $this->session=$session;
        $this->responder = $responder;
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
        
        $viewData = [
            'invoice'=> $this->invoiceFinder->findInvoicePackings($params),
            'invoiceDetails' => $this->invoiceFinder->findInvoiceDetails($params),
            'user_login' => $this->session->get('user'),
        ]; 

        return $this->twig->render($response, 'web/invoiceDetails.twig',$viewData);
    }
}
