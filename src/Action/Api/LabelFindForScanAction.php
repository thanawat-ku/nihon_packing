<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LabelSearchAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        LabelFinder $finder,
        ProductFinder $productFinder,
        LabelUpdater $updater,
        Responder $responder
    ) {

        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $data = (array)$request->getParsedBody();

        $labelNO = $data['label_no'];

        $labels = $this->finder->findLabelNonfullys($data);


        function search($labels, $labelNO)
        {
            $n = sizeof($labels);
            for ($i = 0; $i < $n; $i++) {
                if ($labels[$i]['label_no'] == $labelNO)
                    return $i;
            }
            return -1;
        }

        $result = search($labels, $labelNO);

        if ($result != -1) {
            if ($result["status"] == "MERGING" || $result["status"] == "MERGED") {
                $array_label = array("error");

                $rtdata['error'] = true;
                $rtdata['labels'] = $array_label;
            } else {
                $rtdata['error'] = false;
                $rtdata['labels'] = $result;
            }
        } else {
            $array_label = array("error");

            $rtdata['error'] = true;
            $rtdata['labels'] = $array_label;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
