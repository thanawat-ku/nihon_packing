<?php

namespace App\Action\Api;

use App\Domain\Scrap\Service\ScrapFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ScrapAction
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
    public function __construct(ScrapFinder $productFinder, Responder $responder)
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

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }

        $rtdata['message'] = "Get Scrap Successful";
        $rtdata['error'] = false;
        $rtdata['scraps'] = $this->productFinder->findScraps($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
