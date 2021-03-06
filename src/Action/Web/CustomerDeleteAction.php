<?php

namespace App\Action\Web;

use App\Domain\Customer\Service\CustomerUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class CustomerDeleteAction
{
    private $responder;
    private $updater;
    public function __construct(Responder $responder, CustomerUpdater $updater)
    {
        $this->responder = $responder;
        $this->updater = $updater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $customerId = $data["id"];
        

        // Invoke the Domain with inputs and retain the result
        $data2['is_delete'] = "Y"; 
        $this->updater->updateCustomer($customerId, $data2);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"customers");
    }
}
