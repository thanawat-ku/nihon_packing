<?php

namespace App\Action\Api;

use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class ScrapDetailDeleteAction
{
    private $responder;
    private $updater;
    private $finder;


    public function __construct(Responder $responder,  ScrapDetailUpdater $updater, ScrapDetailFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $scrapDetailID = $data['scrap_detail_id'];

        $this->updater->deleteScrapDetail($scrapDetailID);

        $rtscrap = $this->finder->findScrapDetails($data);

        $rtdata['message'] = "Get Lot Defect Successful";
        $rtdata['error'] = false;
        $rtdata['scrap_detail'] = $rtscrap[0]; 

        return $this->responder->withJson($response, $rtdata);
    }
}
