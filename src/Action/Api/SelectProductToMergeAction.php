<?php

namespace App\Action\Api;

use App\Domain\SelectProductToMerge\Service\SelectProductToMergeFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SelectProductToMergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,SelectProductToMergeFinder $finder,SelectProductToMergeFinder $productFinder,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get Product Successful";
        $rtdata['error']=false;
        $rtdata['products']=$this->finder->findSelectProductToMerges($params);

        return $this->responder->withJson($response, $rtdata);
    }
}