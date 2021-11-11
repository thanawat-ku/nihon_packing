<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CheckSellLabelScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $upmergepack;
    private $upmergepackdetail;
    private $mergepackDetailFinder;
    private $updatesell;
    private  $updateselllabel;

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        SellUpdater $updatesell,
        LabelUpdater $updater,
        Session $session,
        Responder $responder,
        SellLabelUpdater $updateselllabel,
        SellLabelFinder $finderselllabel,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updatesell = $updatesell;
        $this->updateselllabel = $updateselllabel;
        $this->session = $session;
        $this->responder = $responder;
        $this->finderselllabel = $finderselllabel;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];
        $labels = $data['label'];
        $sellID = $data['sell_id'];
        // $product_id = $data['product_id'];

        $upstatus['up_status'] = "SELECTING_LABEL";
        $this->updatesell->updateSellStatus($sellID, $upstatus, $user_id);
        $this->updateselllabel->deleteSellLabelApi($sellID);
        // $this->updater->updateSellLabelDefalt($merge_pack_id, $data, $user_id);

        $arrlabel = explode("#", $labels);

        for ($i = 1; $i < count($arrlabel); $i++) {
            $label_no = $arrlabel[$i];
            $data1['label_no'] = explode(",", $label_no)[0];
            $label_row = $this->finder->findLabelSingleTable($data1);

            if ($label_row) {
                $label_row[0]['sell_id'] = $sellID;
                // $label_row['check_product_id'] = $product_id;
                $this->updateselllabel->insertSellLabelApi($label_row, $user_id);
                // $this->updater->updateSellLabelStatusMerging($id, $label_row, $user_id);
                if ($label_row[0]['lot_id'] != 0) {
                    // $data["merge_pack_id"] = $merge_pack_id;
                    $rtdata['message'] = "Get SellLabel Successful";
                    $rtdata['error'] = false;
                    $rtdata['mpd_from_lots'] = $this->finderselllabel->findSellLabelForlots($data);
                } else {
                    // $data["merge_pack_id"] = $merge_pack_id;
                    $rtdata['message'] = "Get SellLabel Successful";
                    $rtdata['error'] = false;
                    $rtdata['mpd_from_merges'] = $this->finderselllabel->findSellLabelForMergePacks($data);
                }
            } else {
                $rtdata['message'] = "Get SellLabel Successful";
                $rtdata['error'] = true;
                break;
            }
        }

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) {
            $rtdata['check_label_from_sl']="lot";
        }else if($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null){
            $rtdata['check_label_from_sl']="merge";
        }else if($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) {
            $rtdata['check_label_from_sl']="lot_and_merge";
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
