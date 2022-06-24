<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Product\Service\ProductFinder;

/**
 * Action.
 */
final class MergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $productFinder;
    private $mergeDetailFinder;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        MergePackFinder $finder,
        Session $session,
        Responder $responder,
        ProductFinder $productFinder,
        MergePackDetailFinder $mergeDetailFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->productFinder = $productFinder;
        $this->mergeDetailFinder = $mergeDetailFinder;
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

        if(isset($params['startDate'])){
            setcookie("startDateMerge", $params['startDate'] , time() + 3600);
            setcookie("endDateMerge", $params['endDate'], time() + 3600);
        }
        else if(isset($_COOKIE['startDateMerge'])){
            $params['startDate'] = $_COOKIE['startDateMerge'];
            $params['endDate'] = $_COOKIE['endDateMerge'];
        }
        else{
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            setcookie("startDateMerge", $params['startDate'] , time() + 3600);
            setcookie("endDateMerge", $params['endDate'], time() + 3600);
        }

        if (isset($params['search_product_id']) || isset($params['search_status'])) {
            setcookie("search_product_id_merge", $params['search_product_id'], time() + 43200);
            setcookie("search_merge_status", $params['search_status'], time() + 43200);

        } else if (isset($_COOKIE['search_product_id_merge']) || isset($_COOKIE['search_product_id_merge'])) {
            $params['search_product_id'] = $_COOKIE['search_product_id_merge'] ?? 2713;
            $params['search_status'] = $_COOKIE['search_merge_status'] ?? 'CREATED';
        } else {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'CREATED';
            setcookie("search_product_id_merge", $params['search_product_id'], time() + 43200);
            setcookie("search_merge_status", $params['search_status'], time() + 43200);
        }

        $mergePack = $this->finder->findMergePacks($params);

        $viewData = [
            'mergePacks' => $mergePack, 
            'products' => $this->productFinder->findProducts($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],    
        ];


        return $this->twig->render($response, 'web/merges.twig', $viewData); //-----edit twig
    }
}
