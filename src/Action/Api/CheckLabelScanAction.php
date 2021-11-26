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
final class CheckLabelScanAction
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
        $label = $data['label'];
        $mergePackID = $data['id'];
        $productID = $data['product_id'];

        $this->upmergepack->updateMergingApi($mergePackID, $data, $user_id);
        $this->upmergepackdetail->deleteMergePackDetailApi($mergePackID);

        $arrlabel = explode("#", $label);

        for ($i = 1; $i < count($arrlabel); $i++) {
            $label_no = $arrlabel[$i];
            $data1['label_no'] = explode(",", $label_no)[0];
            $label_row = $this->finder->findLabelSingleTable($data1);

            if ($label_row) {
                if ($label_row[0]['mergo_pack_id'] == 0) {
                    $label_row['check_mp_id'] = $mergePackID;
                    $label_row['check_product_id'] = $productID;
                    $id = $this->upmergepackdetail->insertMergePackDetailCheckApi($label_row, $user_id);
                    $this->updater->updateLabelStatusMerging($id, $label_row, $user_id);

                    if ($label_row[0]['lot_id'] != 0) {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtdata['mpd_from_lots'] = $this->mergepackDetailFinder->findMergePackDetailFromLots($data);
                    } else {
                        $data["merge_pack_id"] = $mergePackID;
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $rtdata['mpd_from_merges'] = $this->mergepackDetailFinder->findMergePackDetailFromMergePacks($data);
                    }
                } else {
                    $rtdata['message'] = "Get Label Successful";
                    $rtdata['error'] = true;
                }
            } else {
            }
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
