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
final class SplitLabelAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $updater;
    private $finder;

    public function __construct(
        SplitLabelUpdater $updater,
        SplitLabelFinder $finder,
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

        $this->updater->insertSplitLabelApi($labelID ,$params,$user_id);

        $rtdata['message'] = "Registor Label Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $this->finder->findSplitLabels($params);



        return $this->responder->withJson($response, $rtdata);
    }
}
