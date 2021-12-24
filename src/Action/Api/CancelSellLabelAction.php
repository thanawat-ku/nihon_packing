<?php

namespace App\Action\Api;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CancelSellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findermpd;
    private $updater;
    private $updateLabel;
    private $upmergepackdetail;

    public function __construct(
        SellLabelFinder $finder,
        SellLabelUpdater $updater,
        LabelUpdater $updateLabel,
        Responder $responder
    ) {
        $this->finder = $finder;
        $this->updater = $updater;
        $this->responder = $responder;
        $this->updateLabel = $updateLabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SellLabelID = $data['id'];
        $labelID = $data['label_id'];
        $user_id = $data['user_id'];

        $rtSellLabel = $this->finder->findSellLabels($data);

        if ($rtSellLabel[0]['status'] == "SELLING") {
            $upStatus['status'] = "PACKED";
            $this->updateLabel->updateLabelApi($labelID, $upStatus, $user_id);
        }

        $this->updater->deleteLabelInSellLabel($SellLabelID);

        return $this->responder->withJson($response, $data);
    }
}
