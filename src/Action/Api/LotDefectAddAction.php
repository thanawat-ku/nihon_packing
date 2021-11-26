<?php

namespace App\Action\Api;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
use App\Domain\Defect\Service\DefectFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotDefectAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $defectFinder;


    public function __construct(
        Responder $responder,
        LotDefectUpdater $updater,
        LotDefectFinder $finder,
        DefectFinder $defectFinder
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->defectFinder = $defectFinder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        // find from defect code
        $data = $this->defectFinder->findDefects($params);
        $defectId['defect_id'] = $data[0]['id'];
        $defectId['lot_id'] = $params['lot_id'];
        $defectId['quantity'] = $params['quantity'];
        $this->updater->insertLotDefectApi($defectId, $user_id);

        $rtdata['message'] = "Get Lot Defect Successful";
        $rtdata['error'] = false;
        $rtdata['lot_defects'] = $this->finder->findLotDefects($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
