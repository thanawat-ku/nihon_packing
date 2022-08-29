<?php

namespace App\Action\Web;

use App\Domain\ReportAll\Service\ReportAllFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ReportAllAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $productFinder;
    private $session;
    private $reportAllFinder;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        ReportAllFinder $finder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        // $this->finder = $finder;
        $this->reportAllFinder=$finder;
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
        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-30 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }
        
        $viewData = [
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'products' => $this->reportAllFinder->findReportAll($params),
        ];

        return $this->twig->render($response, 'web/reportAll.twig',$viewData);
    }
}
