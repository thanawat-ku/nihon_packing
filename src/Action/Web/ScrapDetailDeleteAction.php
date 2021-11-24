<?php

namespace App\Action\Web;


use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ScrapDetailDeleteAction
{
    private $twig;
    private $finder;
    private $scrapDetailFinder;
    private $responder;
    private $updater;
    private $session;

    public function __construct(Twig $twig, Responder $responder, ScrapFinder $finder, ScrapUpdater $updater, ScrapDetailFinder $scrapDetailFinder,Session $session,)
    {
        $this->twig=$twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->scrapDetailFinder = $scrapDetailFinder;
        $this->session=$session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $id = $this->updater->insertScrap($data);

        $rtScrap['scrap_id'] = $id;

        $scrapRow = $this->finder->findScraps($rtScrap);

        $viewData = [
            'scrapRow' => $scrapRow[0],
            'scrap_detail' => $this->scrapDetailFinder->findScrapDetails($rtScrap),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/scrapDetails.twig', $viewData);
    }
}
