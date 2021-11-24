<?php

namespace App\Action\Web;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\Scrap\Service\ScrapFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ScrapDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $scrapFinder;
    private $session;

    public function __construct(
        Twig $twig,
        ScrapDetailFinder $finder,
        ScrapFinder $scrapFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->scrapFinder = $scrapFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $scrapID = $params['scrap_id'];

        $rtScrap['scrap_id'] = $scrapID;

        $scrapRow = $this->scrapFinder->findScraps($rtScrap);
        $viewData = [
            'scrapRow' => $scrapRow[0],
            'scrap_details' => $this->finder->findScrapDetails($params),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/scrapDetails.twig', $viewData);
    }
}
