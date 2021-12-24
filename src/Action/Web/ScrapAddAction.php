<?php

namespace App\Action\Web;


use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\Section\Service\SectionFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Defect\Service\DefectFinder;
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
final class ScrapAddAction
{
    private $twig;
    private $finder;
    private $sectionFinder;
    private $productFinder;
    private $defectFinder;
    private $scrapDetailFinder;
    private $responder;
    private $updater;
    private $session;

    public function __construct(Twig $twig, Responder $responder, 
    ScrapFinder $finder,SectionFinder $sectionFinder,ProductFinder $productFinder,DefectFinder $defectFinder, 
    ScrapUpdater $updater, ScrapDetailFinder $scrapDetailFinder,Session $session)
    {
        $this->twig=$twig;
        $this->finder = $finder;
        $this->sectionFinder = $sectionFinder;
        $this->productFinder =$productFinder;
        $this->defectFinder = $defectFinder; 
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
        $scrapRow[0]['check_scrap']="false";

        $viewData = [
            'scrapRow' => $scrapRow[0],
            'sections' => $this->sectionFinder->findSections($data),
            'products' => $this->productFinder->findProducts($data),
            'defects' => $this->defectFinder->findDefects($data),
            'scrap_detail' => $this->scrapDetailFinder->findScrapDetails($rtScrap),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/scrapDetails.twig', $viewData);
    }
}
