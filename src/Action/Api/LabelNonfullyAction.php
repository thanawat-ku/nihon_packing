<?php

namespace App\Action\Api;

use App\Domain\LabelNonfully\Service\LabelNonfullyFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelNonfullyAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,LabelNonfullyFinder $finder,ProductFinder $productFinder,
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
        
        $rtdata['message']="Get LabelNonfully Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabelNonfullys($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}