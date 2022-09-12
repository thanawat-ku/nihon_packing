<?php

namespace App\Action\Web;

use App\Domain\Product\Service\ProductFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ReportScrapAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $productFinder;
    private $lotFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        ProductFinder $productFinder,
        LotFinder $lotFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->productFinder = $productFinder;
        $this->lotFinder = $lotFinder;
        $this->session = $session;
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
        if (!isset($params['startDate'])) {
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
        }

        $viewData = [
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'products' => $this->productFinder->findProducts($params),
            'mfg_lot_no' => $this->lotFinder->findLotsNo($params),
        ];

        return $this->twig->render($response, 'web/reportScrap.twig', $viewData);
    }
}
