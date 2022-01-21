<?php

namespace App\Action\Api;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergePackDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $mergePackFinder;

    public function __construct(
        Twig $twig,
        MergePackDetailFinder $finder,
        MergePackFinder $mergePackFinder,
        MergePackUpdater $updater,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->mergePackFinder = $mergePackFinder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $mergePackID = $params['merge_pack_id'];
        $user_id = $params['user_id'];

        $rtdata['message'] = "Get MergePackDetail Successful";
        $rtdata['error'] = false;

        $rtdata['mpd_from_lots'] = $this->finder->findMergePackDetailFromLots($params);
        $rtdata['mpd_from_merges'] = $this->finder->findMergePackDetailFromMergePacks($params);
        $rtMergePack = $this->mergePackFinder->findMergePacks($params);

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) {
            $rtdata['check_label_from_mpd'] = "lot";
            if ($rtMergePack[0]['merge_status'] == "CREATED" || $rtMergePack[0]['merge_status'] == "MERGING") {
                $upStatus['merge_status'] = "MERGING";
                $this->updater->updateMergePackApi($mergePackID, $upStatus, $user_id);
            }
        } else if ($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null) {
            $rtdata['check_label_from_mpd'] = "merge";

            if ($rtMergePack[0]['merge_status'] == "CREATED" || $rtMergePack[0]['merge_status'] == "MERGING") {
                $upStatus['merge_status'] = "MERGING";
                $this->updater->updateMergePackApi($mergePackID, $upStatus, $user_id);
            }
        } else if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) {
            $rtdata['check_label_from_mpd'] = "lot_and_merge";

            if ($rtMergePack[0]['merge_status'] == "CREATED" || $rtMergePack[0]['merge_status'] == "MERGING") {
                $upStatus['merge_status'] = "MERGING";
                $this->updater->updateMergePackApi($mergePackID, $upStatus, $user_id);
            }
        }

        if ($rtdata['mpd_from_lots'] == null && $rtdata['mpd_from_merges'] == null) {
            if ($rtMergePack[0]['merge_status'] == "MERGING") {
                $upStatus['merge_status'] = "CREATED";
                $this->updater->updateMergePackApi($mergePackID, $upStatus, $user_id);
            }
        }
        return $this->responder->withJson($response, $rtdata);
    }
}
