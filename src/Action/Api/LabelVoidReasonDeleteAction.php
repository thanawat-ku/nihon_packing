<?php

namespace App\Action\Api;

use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class LabelVoidReasonDeleteAction
{
    private $responder;
    private $updater;
    private $finder;


    public function __construct(Responder $responder,  LabelVoidReasonUpdater $updater, LabelVoidReasonFinder $finder)
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
        $voidReasonID = $data['id'];

        $this->updater->deleteLabelVoidReason($voidReasonID);

        $rtdata['message'] = "Get Lot Defect Successful";
        $rtdata['error'] = false;
        $rtdata['label_void_reasons'] = $this->finder->findLabelVoidReasons($data);

        return $this->responder->withJson($response, $rtdata);
    }
}
