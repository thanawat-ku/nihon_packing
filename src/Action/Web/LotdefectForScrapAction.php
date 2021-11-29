<?php

namespace App\Action\Web;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\Section\Service\SectionFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotdefectForScrapAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $sectionFinder;
    private $scrapFinder;
    private $session;

    public function __construct(
        Twig $twig,
        LotDefectFinder $finder,
        ScrapFinder $scrapFinder,
        SectionFinder $sectionFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->sectionFinder = $sectionFinder;
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
            'sections' => $this->sectionFinder->findSections($params),
            'lotDefects' => $this->finder->findLotDefects($params),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/selectDefectForScraps.twig', $viewData);
    }
}
