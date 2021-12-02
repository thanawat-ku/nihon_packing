<?php

namespace App\Action\Web;


use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ScrapAcceptAction
{
    private $twig;
    private $finder;
    private $updateScrapDetail;
    private $responder;
    private $updater;
    private $session;

    public function __construct(Twig $twig, Responder $responder, ScrapFinder $finder, ScrapUpdater $updater, ScrapDetailUpdater $updateScrapDetail, Session $session,)
    {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateScrapDetail = $updateScrapDetail;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $scrapID = $data['id'];

        $data['scrap_status']="ACCECTED";
        $this->updater->updateScrap($scrapID, $data);

        if (!isset($data['startDate'])) {
            $data['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $data['endDate'] = date('Y-m-d');
        }

        $viewData = [
            'scraps' => $this->finder->findScraps($data),
            'user_login' => $this->session->get('user'),
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
        ];

        return $this->twig->render($response, 'web/scraps.twig', $viewData);
    }
}
