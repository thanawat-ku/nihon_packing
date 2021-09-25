<?php

namespace App\Action\Api;

use App\Domain\Customer\Service\CustomerFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CustomerAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(CustomerFinder $finder, Responder $responder)
    {
        $this->finder = $finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message'] = "Get Customer Successful";
        $rtdata['error'] = false;
        $rtdata['customers'] = $this->finder->findCustomers($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
