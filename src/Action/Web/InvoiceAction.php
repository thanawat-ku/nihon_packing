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
final class InvoiceAction
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
    public function __construct(Twig $twig, InvoiceFinder $invoiceFinder, CustomerFinder $customerFinder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->invoiceFinder = $invoiceFinder;
        $this->customerFinder = $customerFinder;
        $this->session = $session;
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

        if (!$this->session->get('startDateInvoice')) {
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');

            //กำหนด session ให้กับ  startDatePack และ endDatePack
            $this->session->start();

            $this->session->set('startDateInvoice', $params['startDate']);
            $this->session->set('endDateInvoice', $params['endDate']);
        } else {

            //กำหนด session ให้กับ  startDateTag และ endDateTag
            if (isset($params['startDate'])) {
                $this->session->start();

                $this->session->set('startDateInvoice', $params['startDate']);
                $this->session->set('endDateInvoice', $params['endDate']);
            }else{
                $params['startDate'] = $this->session->get('startDateInvoice');
                $params['endDate'] = $this->session->get('endDateInvoice');
            }
        }

        $rtCustomer = $this->customerFinder->findCustomers($params);
        if (!isset($params['search_customer_id'])) {
            $params['search_customer_id'] = $rtCustomer[0]['id'];
        }
        if (!isset($params['search_invoice_status'])) {
            $params['search_invoice_status'] = 'ALL';
        }
        $viewData = [
            'customers' => $rtCustomer,
            'invoices' => $this->invoiceFinder->findInvoicePackings($params),
            'search_customer_id' => $params['search_customer_id'],
            'search_invoice_status' => $params['search_invoice_status'],
            'user_login' => $this->session->get('user'),
            'startDate' => $this->session->get('startDateInvoice'),
            'endDate' => $this->session->get('endDateInvoice'),
        ];

        return $this->twig->render($response, 'web/invoices.twig', $viewData);
    }
}
