<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelScanMergeAction
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

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        MergePackUpdater $upmergepack,
        LabelUpdater $updater,
        Session $session,
        Responder $responder,
        MergePackDetailUpdater $upmergepackdetail,
        MergePackDetailFinder $mergepackDetailFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->upmergepack = $upmergepack;
        $this->upmergepackdetail = $upmergepackdetail;
        $this->session = $session;
        $this->responder = $responder;
        $this->mergepackDetailFinder = $mergepackDetailFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];

        $labelRow = $this->finder->findLabelSingleTable($data);

        if (($labelRow[0]['label_type'] == "NONFULLY" || $labelRow[0]['label_type'] == "MERGE_NONFULLY") && $labelRow[0]['status'] == "PACKED") {
            $lotID = $labelRow[0]['lot_id'];
            $mergePackID = $labelRow[0]['merge_pack_id'];
        
            if ($lotID != 0) {
                $dataLotAndMerge['lot_id'] = $lotID;

                $rtLabelFromLot = $this->finder->findCreateMergeNoFromLabels($dataLotAndMerge);

                $productID = $rtLabelFromLot[0]['product_id'];
                $rtLabel['label_id'] = $labelRow[0]['id'];
                $rtLabel['product_id'] = $productID;
                $id = $this->upmergepack->insertMergePackApi($rtLabel, $user_id);
                $this->upmergepack->updateMergingApi($id, $rtLabel, $user_id);
                $rtLabel['merge_pack_id'] = $id;

                $id = $this->upmergepackdetail->insertMergePackDetailFromScanApi($rtLabel, $user_id);
                $rtLabel['status'] = "MERGING";
                $this->updater->updateLabelMergePackApi($id, $rtLabel, $user_id);


                $dataMergeDetail['merge_pack_id'] = $rtLabel['merge_pack_id'];
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = false;
                $rtdata['mpd_from_lots'] = $this->mergepackDetailFinder->findMergePackDetailFromLots($dataMergeDetail);
            } elseif ($mergePackID != 0) {
                $dataLotAndMerge['merge_pack_id'] = $mergePackID;

                $rtLabelFromMerge = $this->finder->findLabelCreateFromMerges($dataLotAndMerge);

                $productID = $rtLabelFromMerge[0]['product_id'];
                $rtLabel['label_id'] = $labelRow[0]['id'];
                $rtLabel['product_id'] = $productID;
                $id = $this->upmergepack->insertMergePackApi($rtLabel, $user_id);
                $this->upmergepack->updateMergingApi($id, $rtLabel, $user_id);
                $rtLabel['merge_pack_id'] = $id;

                $id = $this->upmergepackdetail->insertMergePackDetailFromScanApi($rtLabel, $user_id);
                $this->updater->updateLabelMergePackApi($id, $rtLabel, $user_id);
                
                $dataMergeDetail['merge_pack_id'] = $rtLabel['merge_pack_id'];
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = false;
                $rtdata['mpd_from_merges'] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($dataMergeDetail);
            }
        } else {
            $rtdata['message'] = "Get Label Successful";
            $rtdata['error'] = true;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
