<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Slim\Views\Twig;

/**
 * Action.
 */
final class LabelSplitAction
{
    private $twig;
    private $session;
    private $responder;
    private $updater;
    private $splitupdater;
    private $updaterSpiteLabelDetail;
    private $finder;
    private $splitLabelFinder;

    public function __construct(
        Twig $twig,
        Responder $responder,
        LabelUpdater $updater,
        SplitLabelUpdater $splitupdater,
        Session $session,
        SplitLabelDetailUpdater $updaterSpiteLabelDetail,
        LabelFinder $finder,
        SplitLabelFinder $splitLabelFinder,
    ) {
        $this->twig = $twig;
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->splitupdater = $splitupdater;
        $this->updaterSpiteLabelDetail = $updaterSpiteLabelDetail;
        $this->splitLabelFinder = $splitLabelFinder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body

        $data = (array)$request->getParsedBody();
        $data['status'] = "VOID";
        $labelID = $data['label_id'];
        $this->updater->updateLabel($labelID, $data);
        $dataSP['status'] = "CREATED";
        $dataSP['label_id'] = $labelID;
        $user_id = $this->session->get('user')["id"];
        $splitID = $this->splitupdater->insertSplitLabelApi($dataSP, $user_id);

        $fdata['label_id'] = $data['label_id'];
        $findData = $this->finder->findLabels($fdata);

        $dataDeatail['label_type'] = $findData[0]['label_type'];
        $dataDeatail['lot_id'] = $findData[0]['lot_id'];
        $dataDeatail['user_id']  = $user_id;
        $dataDeatail['merge_pack_id'] = $findData[0]['merge_pack_id'];
        $dataDeatail['quantity1'] = $data['qty1'];
        $dataDeatail['quantity2'] = $data['qty2'];
        $labelDetail = $this->updater->genSplitLabel($dataDeatail);

        for ($i = 0; $i < sizeof($labelDetail); $i++) {
            $dataDetailSL['label_id'] = $labelDetail[$i]['id'];
            $dataDetailSL['split_label_id'] = $splitID;
            $this->updaterSpiteLabelDetail->insertSplitLabelDetailDeatilApi($dataDetailSL, $user_id);
        }
        $findSplitLabel['label_id'] = $dataDetailSL['label_id'];
        
        $viewData = [
            'splitLabels' => $this->splitLabelFinder->findSplitLabels($$findSplitLabel),
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/splitLabels.twig', $viewData);
    }
}
