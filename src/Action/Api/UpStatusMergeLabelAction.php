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
final class UpStatusMergeLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(LabelFinder $finder, LabelUpdater $updater,
    Responder $responder)
    {
        $this->finder=$finder;
        $this->updater=$updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        // $merge_status=$params["merge_status"];
        $user_id=$params["user_id"];
        $labelNo=$params["label_no"];

        $this->updater->updateLabelStrApi($labelNo, $params, $user_id);

        $rtdata['message']="Get MergePack Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabels($params);


        return $this->responder->withJson($response, $rtdata);

        

    }
}