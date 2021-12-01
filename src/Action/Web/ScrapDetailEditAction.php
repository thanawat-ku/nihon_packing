<?php

namespace App\Action\Web;


use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ScrapDetailEditAction
{
    private $twig;
    private $finder;
    private $updater;
    private $scrapFinder;
    private $updateScrapDetail;
    private $scrapDetailFinder;
    private $responder;
    private $session;

    public function __construct(Twig $twig, Responder $responder, LotDefectFinder $finder, LotDefectUpdater $updater, ScrapFinder $scrapFinder, ScrapDetailUpdater $updateScrapDetail, ScrapDetailFinder $scrapDetailFinder, Session $session,)
    {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->scrapFinder = $scrapFinder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateScrapDetail = $updateScrapDetail;
        $this->scrapDetailFinder = $scrapDetailFinder;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $scrapID = $data['scrap_id'];
        $scrapDetailID = $data['id'];
        $this->updateScrapDetail->updateScrapDetail($scrapDetailID, $data);

        $viewData = [
            'scrap_id' => $scrapID,
            'user_login' => $this->session->get('user'),
        ];

        return $this->responder->withRedirect($response, "scrap_details", $viewData);
    }
}
