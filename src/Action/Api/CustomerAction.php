<?php

namespace App\Action\Api;

use App\Domain\Customer\Service\CustomerFinder;
<<<<<<< HEAD
use App\Domain\Product\Service\ProductFinder;
=======
>>>>>>> tae
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

<<<<<<< HEAD
    public function __construct(Twig $twig,CustomerFinder $finder,ProductFinder $productFinder,
=======
    public function __construct(Twig $twig,CustomerFinder $finder,
>>>>>>> tae
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
<<<<<<< HEAD
        $this->productFinder=$productFinder;
=======
>>>>>>> tae
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get Customer Successful";
        $rtdata['error']=false;
        $rtdata['customers']=$this->finder->findCustomers($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
