<?php

namespace App\Action\Web;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SplitLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $productFinder;

    public function __construct(
        Twig $twig,
        SplitLabelFinder $finder,
        Session $session,
        Responder $responder,
        ProductFinder $productFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->productFinder = $productFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if (isset($params['search_product_id']) || isset($params['search_status'])) {
            setcookie("search_product_id_split", $params['search_product_id'], time() + 43200);
            setcookie("search_status_split", $params['search_status'], time() + 43200);

        } else if (isset($_COOKIE['search_product_id_split']) || isset($_COOKIE['search_status_split'])) {
            $params['search_product_id'] = $_COOKIE['search_product_id_split'] ?? 2713;
            $params['search_status'] = $_COOKIE['search_status_split'] ?? 'PRINTED';
        } else {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'PRINTED';
            setcookie("search_product_id_split", $params['search_product_id'], time() + 43200);
            setcookie("search_status_split", $params['search_status'], time() + 43200);
        }

        $viewData = [
            'splitLabels' => $this->finder->findSplitLabels($params),
            'user_login' => $this->session->get('user'),
            'products' => $this->productFinder->findProducts($params),
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],
        ];


        return $this->twig->render($response, 'web/splitLabels.twig', $viewData);
    }
}
