<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class LabelRegisterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $finder;
    private $lotFinder;
    private $lotupdater;

    public function __construct(
        LotUpdater $lotupdater,
        LabelUpdater $updater,
        LabelFinder $finder,
        LotFinder $lotFinder,
    ) {
        $this->updater = $updater;
        $this->finder = $finder;
        $this->lotupdater = $lotupdater;
        $this->lotFinder = $lotFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $lot_id = $params['lot_id'];
        $user_id = $params["user_id"];
        $getLot['lot_id'] = $lot_id;
        $lot = $this->lotFinder->findLotsSingleTalbe($params);
        if ($lot[0]['status'] == "PRINTED") {
            $data['status'] = "PACKED";
            $this->updater->registerLabelApi($lot_id, $data, $user_id);
            $this->lotupdater->registerLotApi($lot_id, $data, $user_id);
            $rtdata['message'] = "Registor Label Successful";
            $rtdata['error'] = false;
            $rtdata['labels'] = $this->finder->findLabels($params);

            return $this->responder->withJson($response, $rtdata);
        } else {
            $rtdata['message'] = "Registor Label fail";
            $rtdata['error'] = true;
            $rtdata['labels'] = null;

            return $this->responder->withJson($response, $rtdata);
        }
    }
}
