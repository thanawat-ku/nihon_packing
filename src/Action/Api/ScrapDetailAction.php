<?php

namespace App\Action\Api;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Domain\Scrap\Service\ScrapFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ScrapDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findScrapDetails;
    private $updateScrap;
    private $finder;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(ScrapDetailFinder $findScrapDetails, Responder $responder, ScrapUpdater $updateScrap, ScrapFinder  $finder)
    {

        $this->findScrapDetails = $findScrapDetails;
        $this->responder = $responder;
        $this->updateScrap = $updateScrap;
        $this->finder = $finder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $user_id = $params['user_id'];
        $scrapID = $params['scrap_id'];

        $rtScrapDetail = $this->findScrapDetails->findScrapDetails($params);
        $rtScrap = $this->finder->findScraps($params);

        if ($rtScrap[0]['scrap_status'] == "CREATED" || $rtScrap[0]['scrap_status'] == "SELECTING") {
            if ($rtScrapDetail) {
                $upStatus['scrap_status'] = "SELECTING";
                $this->updateScrap->updateScrapApi($scrapID, $upStatus, $user_id);
            } else {
                $upStatus['scrap_status'] = "CREATED";
                $this->updateScrap->updateScrapApi($scrapID, $upStatus, $user_id);
            }
        }

        $rtdata['message'] = "Get ScrapDetail Successful";
        $rtdata['error'] = false;
        $rtdata['scrap_details'] = $this->findScrapDetails->findScrapDetails($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
