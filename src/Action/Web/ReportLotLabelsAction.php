<?php

namespace App\Action\Web;

// use App\Domain\ReportAll\Service\ReportAllFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ReportLotLabelsAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $productFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
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
        
        
        $viewData = [
        ];

        return $this->twig->render($response, 'web/reportLotLabels.twig',$viewData);
    }
}
