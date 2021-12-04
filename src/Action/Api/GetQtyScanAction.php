<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class GetQtyScanAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $findermergepackdetail;
    private $findermergepack;

    public function __construct(
        LabelFinder $finder,
        MergePackUpdater $updater,
        MergePackDetailFinder $findermergepackdetail,
        Responder $responder,
        MergePackFinder $findermergepack
    ) {

        $this->finder = $finder;
        $this->updater = $updater;
        $this->findermergepackdetail = $findermergepackdetail;
        $this->findermergepack = $findermergepack;
        // $this->productFinder=$productFinder;

        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $productID = $data['product_id'];
        $mergePackID = $data['merge_pack_id'];
        $user_id = $data['user_id'];

        $product = $this->findermergepack->findMergePacks($data);

        $rtdata['mpd_from_lots'] = $this->findermergepackdetail->findMergePackDetailFromLots($data);
        $rtdata['mpd_from_merges'] = $this->findermergepackdetail->findMergePackDetailFromMergePacks($data);

        $std_pack = (int)$product[0]['std_pack'];
        $sum_qty = 0;
        $check_label_error = 0;
        $number_label = 0;
        $merge_status = "MERGING";

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) { //lot
            for ($i = 0; $i < count($rtdata['mpd_from_lots']); $i++) {
                $sum_qty += $rtdata['mpd_from_lots'][$i]['quantity'];
                $number_label += 1;
                if ($rtdata['mpd_from_lots'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_lots'][$i]['status'] != "MERGING") || ($rtdata['mpd_from_lots'][$i]['label_type'] == "FULLY" || $rtdata['mpd_from_lots'][$i]['label_type'] == "MERGE_FULLY") || $rtdata['mpd_from_lots'][$i]['status'] == "MERGED") {
                    $check_label_error += 1;
                }
            }
            $array_labels = array($sum_qty,  $std_pack, $check_label_error, $number_label, $merge_status);
        } else if ($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null) { //merge
            for ($i = 0; $i < count($rtdata['mpd_from_merges']); $i++) {
                $sum_qty += $rtdata['mpd_from_merges'][$i]['quantity'];
                $number_label += 1;
                if ($rtdata['mpd_from_merges'][$i]['merge_pack_id'] != $mergePackID || $rtdata['mpd_from_merges'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_merges'][$i]['status'] != "MERGING") || ($rtdata['mpd_from_merges'][$i]['label_type'] == "FULLY" || $rtdata['mpd_from_merges'][$i]['label_type'] == "MERGE_FULLY")) {
                    $check_label_error += 1;
                }
            }
            $array_labels = array($sum_qty,  $std_pack, $check_label_error, $number_label, $merge_status);
        } else if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) { //lot and merge
            for ($i = 0; $i < count($rtdata['mpd_from_lots']); $i++) {
                $sum_qty += $rtdata['mpd_from_lots'][$i]['quantity'];
                $number_label += 1;
                if ($rtdata['mpd_from_lots'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_lots'][$i]['status'] != "MERGING") || ($rtdata['mpd_from_lots'][$i]['label_type'] == "FULLY" || $rtdata['mpd_from_lots'][$i]['label_type'] == "MERGE_FULLY") || $rtdata['mpd_from_lots'][$i]['status'] == "MERGED") {
                    $check_label_error += 1;
                }
            }
            for ($i = 0; $i < count($rtdata['mpd_from_merges']); $i++) {
                $sum_qty += $rtdata['mpd_from_merges'][$i]['quantity'];
                $number_label += 1;
                if ($rtdata['mpd_from_merges'][$i]['merge_pack_id'] != $mergePackID || $rtdata['mpd_from_merges'][$i]['product_id'] != $productID  || ($rtdata['mpd_from_merges'][$i]['status'] != "MERGING") || ($rtdata['mpd_from_merges'][$i]['label_type'] == "FULLY" || $rtdata['mpd_from_merges'][$i]['label_type'] == "MERGE_FULLY")) {
                    $check_label_error += 1;
                }
            }
            $array_labels = array($sum_qty,  $std_pack, $check_label_error, $number_label, $merge_status);
        }

        if ($rtdata['mpd_from_lots'] == null && $rtdata['mpd_from_merges'] == null) {
            $upStatus['merge_status'] = "CREATED";
            $this->updater->updateMergePackApi($mergePackID, $upStatus, $user_id);
            $merge_status = "CREATED";
            $array_labels = array($sum_qty,  $std_pack, $check_label_error, $number_label, $merge_status);
        }

        $qtyscan = $array_labels;
        return $this->responder->withJson($response, $qtyscan);
    }
}
