<?php

namespace App\Action\Api;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class LotSyncAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        Twig $twig,
        LotFinder $finder,
        LotUpdater $updater,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        //$max_id=$this->finder->getLocalMaxLotId();

        $lots = $this->finder->getSyncLots();
        $rtData = [];

        for ($i = 0; $i < count($lots); $i++) {
            $params1['id'] = $lots[$i]["LotID"];
            $params1['lot_no'] = $lots[$i]["LotNo"];
            $params1['product_id'] = $lots[$i]["ProductID"];
            $params1['quantity'] = $lots[$i]["CurrentQty"];
            $params1['issue_date'] = substr($lots[$i]["IssueDate"], 0, 10);
            try {
                $this->updater->insertLot($params1);
            } catch (\Throwable $th) {
            }
            $rtData = [];
            array_push($rtData, $lots[$i]);
        }

        return $this->responder->withJson($response, $rtData);
    }
}
