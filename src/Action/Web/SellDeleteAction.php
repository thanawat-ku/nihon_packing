<?php

namespace App\Action\Web;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellDeleteAction
{
    private $finder;
    private $findSellLabel;
    private $responder;
    private $updater;
    private $updateLabel;

    public function __construct(Responder $responder, SellFinder $finder, SellLabelFinder $findSellLabel, SellUpdater $updater, LabelUpdater $updateLabel)
    {
        $this->finder = $finder;
        $this->findSellLabel = $findSellLabel;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->updateLabel = $updateLabel;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (int)$data['id'];

        $rtSell =  $this->finder->findSells($data);

        if ($rtSell[0]['sell_status'] == "CONFIRM" || $rtSell[0]['sell_status'] == "TAGGED" || $rtSell[0]['sell_status'] == "INVOICED") {
        } else {
            $rtSell['sell_id'] = $sellID;
            $rtSellLabel =  $this->findSellLabel->findSellLabels($rtSell);

            for ($i = 0; $i < count($rtSellLabel); $i++) {
                $upStatus['status'] = "PACKED";
                $this->updateLabel->updateLabel($rtSellLabel[$i]['label_id'], $upStatus);
            }
            $data['is_delete'] = 'Y';
            $this->updater->updateSell($sellID, $data);
        }

        return $this->responder->withRedirect($response, "sells");
    }
}
