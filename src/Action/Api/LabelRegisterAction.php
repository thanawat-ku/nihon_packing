<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
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

    public function __construct(
        LabelUpdater $updater,
        LabelFinder $finder,
        Responder $responder
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder=$finder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $labelID = $params["label_id"];
        $user_id = $params["user_id"];

        $this->updater->updateLabelApi($labelID ,$params,$user_id);

        $rtdata['message'] = "Registor Label Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $this->finder->findLabels($params);



        return $this->responder->withJson($response, $rtdata);
    }
}
