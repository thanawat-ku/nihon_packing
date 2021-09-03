<?php

namespace App\Action\Api;

use App\Domain\CheckLabel\Service\CheckLabelFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CheckLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,CheckLabelFinder $finder,ProductFinder $productFinder,
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
        
        $rtdata['message']="Get CheckLabel Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findCheckLabels($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}