<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotUpdater;
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
    private $lotupdater;

    public function __construct(
        LotUpdater $lotupdater,
        LabelUpdater $updater,
        LabelFinder $finder,
        Responder $responder
        
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->lotupdater = $lotupdater;

    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $lot_id = $params['lot_id'];
        $user_id = $params["user_id"];

        $this->updater->registerLabelApi($lot_id,$params, $user_id);

        $this->lotupdater->registerLotApi($lot_id,$params,$user_id);
        
        $rtdata['message'] = "Registor Label Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $this->finder->findLabels($params);



        return $this->responder->withJson($response, $rtdata);
    }
}
