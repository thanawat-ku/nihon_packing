<?php

namespace App\Action\Api;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ScrapDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $productFinder;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(ScrapDetailFinder $productFinder, Responder $responder)
    {

        $this->productFinder = $productFinder;
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

        $rtdata['message'] = "Get ScrapDetail Successful";
        $rtdata['error'] = false;
        $rtdata['scrap_details'] = $this->productFinder->findScrapDetails($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
