<?php

namespace App\Action\Web;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $productFinder;
    private $session;

    public function __construct(
        Twig $twig,
        PackFinder $finder,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $checkError = "false";
        if (isset($params['checkError'])) {
            $checkError = "true";
        }

        if(isset($params['startDate'])){
            setcookie("startDatePack", $params['startDate'] , time() + 3600);
            setcookie("endDatePack", $params['endDate'], time() + 3600);
        }
        else if(isset($_COOKIE['startDatePack'])){
            $params['startDate'] = $_COOKIE['startDatePack'];
            $params['endDate'] = $_COOKIE['endDatePack'];
        }
        else{
            $params['startDate'] = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDatePack", $params['startDate'] , time() + 3600);
            setcookie("endDatePack", $params['endDate'], time() + 3600);
        }

        if (isset($params['search_product_id']) || isset($params['search_pack_status'])) {
            setcookie("search_product_id_pack", $params['search_product_id'], time() + 43200);
            setcookie("search_pack_status", $params['search_pack_status'], time() + 43200);

        } else if (isset($_COOKIE['search_product_id_pack']) || isset($_COOKIE['search_status_pack'])) {
            $params['search_product_id'] = $_COOKIE['search_product_id_pack'] ?? 2713;
            $params['search_pack_status'] = $_COOKIE['search_status_pack'] ?? 'ALL';
        } else {
            $params['search_product_id'] = 2713;
            $params['search_pack_status'] = 'ALL';
            setcookie("search_product_id_pack", $params['search_product_id'], time() + 43200);
            setcookie("search_pack_status", $params['search_pack_status'], time() + 43200);
        }

        $viewData = [
            'products' => $this->productFinder->findProducts($params),
            'packs' => $this->finder->findPacks($params),
            'user_login' => $this->session->get('user'),
            'search_product_id' => $params['search_product_id'],
            'search_pack_status' => $params['search_pack_status'],
            'checkError' => $checkError,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];

        return $this->twig->render($response, 'web/packs.twig', $viewData);
    }
}
