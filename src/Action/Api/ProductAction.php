<?php

namespace App\Action\Api;

<<<<<<< HEAD

=======
>>>>>>> tae
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ProductAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

<<<<<<< HEAD
    public function __construct(Twig $twig,ProductFinder $finder,ProductFinder $productFinder,
=======
    public function __construct(Twig $twig,ProductFinder $finder,
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
        
        $rtdata['message']="Get Product Successful";
        $rtdata['error']=false;
<<<<<<< HEAD
        $rtdata['products']=$this->finder->findProducts($params);
=======
        $rtdata['Products']=$this->finder->findProducts($params);
>>>>>>> tae


        
        return $this->responder->withJson($response, $rtdata);
    }
}
