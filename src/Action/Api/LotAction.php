<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotAction
{
    /**
     * @var Responder
     */ 
    private $responder;
    private $finder;

    public function __construct(
        Twig $twig,
        LotFinder $finder,
        ProductFinder $productFinder,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if(isset($params['start_date'])){
            $params['startDate']=$params['start_date'];
            $params['endDate']=$params['end_date'];
        }

        $rtdata['message'] = "Get Lot Successful";
        $rtdata['error'] = false;
        $rtdata['lots'] = $this->finder->findLots($params);

        return $this->responder->withJson($response, $rtdata);
    }
}