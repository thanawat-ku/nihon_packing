<?php

namespace App\Action\Web;


use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ScrapRejectAction
{
    private $finder;
    private $responder;
    private $updater;
    private $session;
    private $updateScrapDetail;

    public function __construct(Responder $responder, ScrapFinder $finder, 
    ScrapUpdater $updater, ScrapDetailUpdater $updateScrapDetail, Session $session)
    {
       
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

        $data['scrap_status']="REJECTED";
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

        return $this->responder->withRedirect($response, "scraps", $viewData);
    }
}
