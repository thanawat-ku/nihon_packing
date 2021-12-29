<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ConfirmSellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $updatelabel;
    private $findSell;
    private $updateTag;

    public function __construct(
        SellLabelFinder $finder,
        LabelUpdater $updatelabel,
        LabelFinder $findLabel,
        SellFinder $findSell,
        TagUpdater $updateTag,
        Session $session,
        Responder $responder,
        SellUpdater $updater
    ) {
        $this->finder = $finder;
        $this->findLabel = $findLabel;
        $this->updatelabel = $updatelabel;
        $this->findSell = $findSell;
        $this->updateTag = $updateTag;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['user_id'];
        $sellID = (int)$data['sell_id'];

        $sellLabel = $this->finder->findSellLabels($data);


        for ($i = 0; $i < count($sellLabel); $i++) {
            $labelID = (int)$sellLabel[$i]['label_id'];
            $dataUpdate['up_status'] = "USED";
            $this->updatelabel->updateLabelStatus($labelID, $dataUpdate, $user_id);
        }
        $updata['up_status'] = "CONFIRM";
        $this->updater->updateSellStatus($sellID, $updata, $user_id);
        $allData = [''];
        
        $rtSell = $this->findSell->findSells($data);

        $rtTag = $this->updateTag->genTagsApi($sellID, $rtSell, $user_id);

        $upStatus['status'] = "PRINTED";
        $this->updateTag->updateTagPrintFromSellIDApi($sellID, $upStatus, $user_id);
        $data['up_status'] = "PRINTED";
        $this->updater->updateSellStatus($sellID, $data, $user_id);


        if (isset($data['start_date'])) {
            $allData['startDate'] = $data['start_date'];
            $allData['endDate'] = $data['end_date'];
        }

        $rtdata['message'] = "Get Sell Successful";
        $rtdata['error'] = false;
        $rtdata['sells'] = $this->findSell->findSells($allData);

        return $this->responder->withJson($response, $rtdata);
    }
}
