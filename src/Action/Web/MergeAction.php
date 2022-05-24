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

        // if (!isset($params['startDate'])) {
        //     $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
        //     $params['endDate'] = date('Y-m-d');
        // }
        if (!isset($params['search_product_id'])) {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'CREATED';
        }

        $mergePack = $this->finder->findMergePacks($params);

        $viewData = [
            'mergePacks' => $mergePack, 
            'products' => $this->productFinder->findProducts($params),
            'user_login' => $this->session->get('user'),
            // 'startDate' => $params['startDate'],
            // 'endDate' => $params['endDate'],
            'search_product_id' => $params['search_product_id'],
            'search_status' => $params['search_status'],    
        ];


        return $this->twig->render($response, 'web/merges.twig', $viewData); //-----edit twig
    }
}
