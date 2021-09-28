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
        // $paramsFL['GG'] = "GG";
        // $findL = $this->labelFinder->findLabels($paramsFL);

        $labels = [];
        // for ($i = 0; $i < sizeof($findL); $i++) {
        //     for ($j = 0; $j < sizeof($findD); $j++) {
        //         $check1 = $findL[$i]['id'];
        //         $check2 = $findD[$j]['label_id'];
        //         if ($check1 == $check2) {
        //             array_push($labels, $findL[$i]);
        //         }
        //     }
        // }
        
        for ($i = 0; $i < sizeof($findD); $i++) {
            $dataL['label_id'] = $findD[$i]['label_id'];
            $findL = $this->labelFinder->findLabels($dataL);
            array_push($labels, $findL[0]);
        }


        $rtdata['message'] = "Get SplitLabelDetail Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $labels;

        return $this->responder->withJson($response, $rtdata);
    }
}
