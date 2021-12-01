<?php

namespace App\Action\Api;

use App\Domain\Scrap\Service\ScrapFinder;
use App\Domain\Scrap\Service\ScrapUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class ScrapAddAction
{
    private $responder;
    private $updater;
    private $finder;


    public function __construct(Responder $responder,  ScrapUpdater $updater, ScrapFinder $finder)
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
        $user_id = $data['user_id'];

        $id = $this->updater->insertScrapApi($data, $user_id);

        $data['scrap_id'] = $id;

        $rtscrap = $this->finder->findScraps($data);

        $rtdata['message'] = "Get Lot Defect Successful";
        $rtdata['error'] = false;
        $rtdata['scrap'] = $rtscrap[0]; 

        return $this->responder->withJson($response, $rtdata);
    }
}
