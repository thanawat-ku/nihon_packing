<?php

namespace App\Action\Web;


use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Domain\LotDefect\Service\LotDefectUpdater;
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
    private $updateLotDefect;
    private $responder;
    private $updater;
    private $session;

    public function __construct(Twig $twig, Responder $responder, ScrapFinder $finder, ScrapDetailUpdater $updater,LotDefectUpdater $updateLotDefect, ScrapDetailFinder $scrapDetailFinder,Session $session,)
    {
        $this->twig=$twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateLotDefect = $updateLotDefect;
        $this->scrapDetailFinder = $scrapDetailFinder;
        $this->session=$session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $scrapID = $data['scrap_id'];
        $scrapDetailID = $data['id'];

        $this->updater->deleteScrapDetail($scrapDetailID);

        $viewData = [
            'scrap_id' => $scrapID, 
            'user_login' => $this->session->get('user'),
        ];

        return $this->responder->withRedirect($response, "scrap_details",$viewData);
    }
}
