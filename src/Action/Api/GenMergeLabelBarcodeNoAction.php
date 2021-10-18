<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class GenMergeLabelBarcodeNoAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $upmergepackdetail;
    private $upmergepack;
    private $findermpdetail;


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        LabelUpdater $updater,
        Session $session,
        Responder $responder,
        MergePackDetailUpdater $upmergepackdetail,
        MergePackUpdater $upmergepack,
        MergePackDetailFinder $findermpdetail
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->upmergepackdetail = $upmergepackdetail;
        $this->upmergepack = $upmergepack;
        $this->findermpdetail = $findermpdetail;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id = (int)$data['user_id'];
        $merge_pack_id = (int)$data['merge_pack_id'];

        $labels = $this->findermpdetail->findMergePackDetails($data);
        for ($i = 0; $i < count($labels); $i++) {
            $dataupdate['id']=$labels[$i]['lb_id'];
            $dataupdate['up_status']="VOID";
            $dataupdate['void']="MERGED";
            $this->updater->updateLabelStatus($dataupdate['id'], $dataupdate, $user_id);
        } 
        $labels = $this->updater->genMergeLabel($data);

        // $this->upmergepackdetail->deleteLabelMergePackDetailApi($merge_pack_id);
        // $this->upmergepackdetail->insertMergePackDetailApi($labels, $user_id);
        $this->upmergepack->updateStatusMergeApi($merge_pack_id, $data, $user_id);

        $rtdata['message'] = "Gen Merge Labels Successful";
        $rtdata['error'] = false;
        $rtdata['labels'] = $labels;
        return $this->responder->withJson($response, $rtdata);
    }
}
