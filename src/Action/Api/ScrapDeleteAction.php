<?php

namespace App\Action\Api;

use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class ScrapDeleteAction
{
    private $responder;
    private $updater;
    private $finder;


    public function __construct(Responder $responder,  ScrapUpdater $updater, ScrapFinder $finder, CpoItemFinder $findcpo_item)
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
        $scrapID = $data['scrap_id'];

        $this->updater->deleteScrapApi($scrapID);

        $rtscrap = $this->finder->findScraps($data);

        $rtdata['message'] = "Get Lot Defect Successful";
        $rtdata['error'] = false;
        $rtdata['scrap'] = $rtscrap[0]; 

        return $this->responder->withJson($response, $rtdata);
    }
}
