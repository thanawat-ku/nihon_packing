<?php

namespace App\Action\Api;

use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Action.
 */
final class SpliteLabelRegisterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $finder;

    public function __construct(
        SplitLabelFinder $finder,
        Responder $responder,
        SplitLabelUpdater  $updater,
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $splitID = $params["split_label_id"];
        $user_id = $params["user_id"];

        $this->updater->registerSplitLabelApi($params, $splitID, $user_id);

        $rtdata['message'] = "Registor Label Successful";
        $rtdata['error'] = false;
        $rtdata['split_labels'] = $this->finder->findSplitLabels($params);



        return $this->responder->withJson($response, $rtdata);
    }
}
