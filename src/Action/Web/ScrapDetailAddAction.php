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
final class ScrapDetailAddAction
{
    private $twig;
    private $finder;
    private $updater;
    private $scrapFinder;
    private $updateScrapDetail;
    private $scrapDetailFinder;
    private $responder;
    private $session;

    public function __construct(Twig $twig, Responder $responder, LotDefectFinder $finder,LotDefectUpdater $updater, ScrapFinder $scrapFinder, ScrapDetailUpdater $updateScrapDetail, ScrapDetailFinder $scrapDetailFinder, Session $session,)
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
        $sectionID = $data['section_id'];

        $rtLotDefect = $this->finder->findLotDefects($data);

        $addScrapDetail['scrap_id'] = $scrapID;
        $addScrapDetail['section_id'] = $sectionID;
        $addScrapDetail['product_id'] = $rtLotDefect[0]['product_id'];
        $addScrapDetail['defect_id'] = $rtLotDefect[0]['defect_id'];
        $addScrapDetail['quantity'] = $rtLotDefect[0]['quantity'];

        $this->updateScrapDetail->insertScrapDetail($addScrapDetail);

        $upstatus['lot_defect_status'] = "ADD_SCRAP";
        $this->updater->updateLotDefect($rtLotDefect[0]['id'], $upstatus);

        $rtScrap['scrap_id'] = $scrapID;
        $scrapRow = $this->scrapFinder->findScraps($rtScrap);

        $viewData = [
            'scrapRow' => $scrapRow[0],
            'scrap_detail' => $this->scrapDetailFinder->findScrapDetails($rtScrap),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/scrapDetails.twig', $viewData);
    }
}
