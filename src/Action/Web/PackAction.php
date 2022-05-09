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

    public function __construct(Twig $twig,PackFinder $finder,ProductFinder $productFinder,
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
        $checkError = "false";
        if (isset($params['checkError'])) {
            $checkError = "true";
        }

        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-30 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }
        
        
        $viewData = [
            'products'=>$this->productFinder->findProducts($params),
            'packs' => $this->finder->findPacks($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'checkError' => $checkError,
        ];
        
        return $this->twig->render($response, 'web/packs.twig',$viewData);
    }
}
