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
        SplitLabelFinder $splitLabelFinder
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
        $labelId = $data['label_id'];
        $printerId = $data['printer_id'];

        $findLabel['label_id'] = $labelId;
        $label = $this->finder->findLabelSingleTable($findLabel);
        $productId = $label[0]['product_id'];
        if ($label[0]['status'] == "PACKED") {
            $user_id = $this->session->get('user')["id"];
            $findSplitLabel['label_id'] = $labelId;
            $dataDeatail['label_type'] = $label[0]['label_type'];
            $dataDeatail['lot_id'] = $label[0]['lot_id'];
            $dataDeatail['user_id']  = $user_id;
            $dataDeatail['merge_pack_id'] = $label[0]['merge_pack_id'];
            $dataDeatail['quantity1'] = $data['qty1'];
            $dataDeatail['quantity2'] = $data['qty2'];
            $dataDeatail['product_id'] = $productId;
            $dataDeatail['printer_id'] = $printerId;
            $dataDeatail['wait_print'] = "Y";

            $dataDeatail['prefer_lot_id'] = $label[0]['prefer_lot_id'];
            
            $labelDetail = $this->updater->genSplitLabel($dataDeatail);
            

            $dataSP['status'] = "PRINTED";
            $dataSP['label_id'] = $labelId;
            $splitID = $this->splitupdater->insertSplitLabelApi($dataSP, $user_id);
            $data2['split_label_id'] = $splitID;
            $data2['label_void_reason_id'] = "1";
            $data2['status'] = "VOID";
            $this->updater->updateLabel($labelId, $data2);
            $dataSplit = $this->splitLabelFinder->findSplitLabels($findSplitLabel);
            
            for ($i = 0; $i < sizeof($labelDetail); $i++) {
                $dataDetailSL['label_id'] = $labelDetail[$i]['id'];
                $dataDetailSL['split_label_id'] = $splitID;
                $this->updaterSpiteLabelDetail->insertSplitLabelDetailDeatilApi($dataDetailSL, $user_id);
            }
        }
        $viewData = [
            'id' => $dataSplit[0]['id'],
        ];

        return $this->responder->withRedirect($response, "label_splitlabel", $viewData);
    }
}
