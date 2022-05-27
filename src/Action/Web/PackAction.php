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

        if (!$this->session->get('startDatePack')) {
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');

            //กำหนด session ให้กับ  startDatePack และ endDatePack
            $this->session->start();

            $this->session->set('startDatePack', $params['startDate']);
            $this->session->set('endDatePack', $params['endDate']);
        } else {
            // กำหนด session ให้กับ  startDatePack และ endDatePack
            if (isset($params['startDate'])) {
                $this->session->start();

                $this->session->set('startDatePack', $params['startDate']);
                $this->session->set('endDatePack', $params['endDate']);
            }else{
                $params['startDate'] = $this->session->get('startDatePack');
                $params['endDate'] = $this->session->get('endDatePack');
            }
        }

        if (!isset($params['search_product_id'])) {
            $params['search_product_id'] = 1;
        }
        if (!isset($params['search_pack_status'])) {
            $params['search_pack_status'] = 'ALL';
        }

        $viewData = [
            'products' => $this->productFinder->findProducts($params),
            'packs' => $this->finder->findPacks($params),
            'user_login' => $this->session->get('user'),
            'search_product_id' => $params['search_product_id'],
            'search_pack_status' => $params['search_pack_status'],
            'checkError' => $checkError,
            'startDate' => $this->session->get('startDatePack'),
            'endDate' => $this->session->get('endDatePack'),
        ];

        return $this->twig->render($response, 'web/packs.twig', $viewData);
    }
}
