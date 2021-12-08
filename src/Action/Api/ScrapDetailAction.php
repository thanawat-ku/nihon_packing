<?php

namespace App\Action\Api;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\Scrap\Service\ScrapUpdater;
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


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(ScrapDetailFinder $findScrapDetails, Responder $responder, ScrapUpdater $updateScrap)
    {

        $this->findScrapDetails = $findScrapDetails;
        $this->responder = $responder;
        $this->updateScrap = $updateScrap;
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

        if ($rtScrapDetail[0]['status'] == "CREATED" || $rtScrapDetail[0]['status'] == "SELECTING") {
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
