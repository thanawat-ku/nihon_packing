<?php

namespace App\Action\Web;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\Defect\Service\DefectFinder;
use App\Domain\Section\Service\SectionFinder;
use App\Domain\Product\Service\ProductFinder;
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
    private $sectionFinder;
    private $productFinder;
    private $defectFinder;
    private $session;

    public function __construct(
        Twig $twig,
        ScrapDetailFinder $finder,
        ScrapFinder $scrapFinder,
        SectionFinder $sectionFinder,
        ProductFinder $productFinder,
        DefectFinder $defectFinder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->scrapFinder = $scrapFinder;
        $this->sectionFinder = $sectionFinder;
        $this->productFinder = $productFinder;
        $this->defectFinder = $defectFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $scrapID = $params['scrap_id'];

        $rtScrap['scrap_id'] = $scrapID;

        $scrapRow = $this->scrapFinder->findScraps($rtScrap);

        $rtScrapDetail = $this->finder->findScrapDetails($params);
        if (!$rtScrapDetail) {
            $scrapRow[0]['check_scrap']="false";
        }else{
            $scrapRow[0]['check_scrap']="true";
        }

        $viewData = [
            'scrapRow' => $scrapRow[0],
            'sections' => $this->sectionFinder->findSections($params),
            'products' => $this->productFinder->findProducts($params),
            'defects' => $this->defectFinder->findDefects($params),
            'scrapDetails' => $this->finder->findScrapDetails($params),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/scrapDetails.twig', $viewData);
    }
}
