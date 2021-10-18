<?php

namespace App\Action\Api;

use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use phpDocumentor\Reflection\Types\Void_;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SplitLabelDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $labelFinder;

    public function __construct(SplitLabelDetailFinder $finder, LabelFinder $labelFinder, Responder $responder)
    {
        $this->finder = $finder;
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $findD = $this->finder->findSplitLabelDetails($params);

        $labels = [];  
        for ($i = 0; $i < sizeof($findD); $i++) {
            $LabelId['label_id'] = $findD[$i]['label_id'];
            $findLabel = $this->labelFinder->findLabels($LabelId);
            array_push($labels, $findLabel[0]);
        }

        $rtdata['message'] = "Get SplitLabelDetail Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $labels;

        return $this->responder->withJson($response, $rtdata);
    }
}
