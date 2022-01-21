<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class CheckLabelScanRegisMergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;


    public function __construct(

        LabelFinder $finder,
        MergepackUpdater $updater,
        Responder $responder

    ) {
        $this->finder = $finder;
        $this->updater = $updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $label = $data['label'];
        $user_id = $data['user_id'];
        $mergepackID = $data['id'];
        $raMergepack['merge_pack_id'] = $mergepackID;

        $arrlabel = explode("#", $label);

        $rtdata['mpd_from_lots'] = [];
        $rtdata['mpd_from_merges'] = [];
        $rtdata['label_no'] = "Null";

        for ($i = 1; $i < count($arrlabel); $i++) {
            $label_no = $arrlabel[$i];
            $labelNo['label_no'] = explode(",", $label_no)[0];
            $labelRow = $this->finder->findLabelSingleTable($labelNo);

            if ($labelRow != null) {
                $labelID['id'] = $labelRow[0]['id'];
                if ($labelRow[0]['merge_pack_id'] == 0) {
                    if ($labelRow[0]['lot_id'] != 0) {
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $labelLots = $this->finder->findCreateMergeNoFromLabels($labelID);
                        array_push($rtdata['mpd_from_lots'], $labelLots[0]);
                    }
                } else {
                    $rtdata['message'] = "Get Label Successful";
                    $rtdata['error'] = false;
                    $labelMergePacks = $this->finder->findLabelFromMergePacks($labelID);
                    array_push($rtdata['mpd_from_merges'], $labelMergePacks[0]);
                }
            } else {
                $rtdata['message'] = "Get Label Successful";
                $rtdata['label_no'] = $labelNo['label_no'];
                $rtdata['error'] = true;
                break;
            }
        }
        $rtLabel = $this->finder->findLabelSingleTable($raMergepack);
        $checkLabelStatus = true;
        for ($i = 0; $i < count($rtLabel); $i++) {
            if ($rtLabel[$i]['status'] != "PRINTED") {
                $checkLabelStatus = false;
                break;
            }
        }
        if ($checkLabelStatus == true) {
            if ($rtdata['mpd_from_lots'] == null && $rtdata['mpd_from_merges'] == null) {
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = true;
                return $this->responder->withJson($response, $rtdata);
            } else {
                $upStatus['merge_status'] = "REGISTERING";
                $this->updater->updateMergePackApi($mergepackID, $upStatus, $user_id);
                return $this->responder->withJson($response, $rtdata);
            }
        } else {
            $rtdata['message'] = "Get Label Successful";
            $rtdata['error'] = "defective";
            return $this->responder->withJson($response, $rtdata);
        }
    }
}
