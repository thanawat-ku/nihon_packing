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
        $data['label_void_reason_id'] = "1";
        $labelID = $data['label_id'];
       
        $dataSP['status'] = "CREATED";
        $dataSP['label_id'] = $labelID;
        $user_id = $this->session->get('user')["id"];
        $splitID = $this->splitupdater->insertSplitLabelApi($dataSP, $user_id);

        $data2['label_id'] = $data['label_id'];
        $findLabel = $this->finder->findLabels($data2);

        $findSplitLabel['label_id'] =   $labelID;
        if(isset($findLabel[0])){
            $dataLabel = $findLabel;
            $dataSplit= $this->splitLabelFinder->findSplitLabels($findSplitLabel);
        }else{
            $dataLabel = $this->finder->findLabelForLotZero($data2);
            $dataSplit= $this->splitLabelFinder->findSplitLabels($findSplitLabel);
        }

        $dataDeatail['label_type'] = $dataLabel[0]['label_type'];
        $dataDeatail['lot_id'] = $dataLabel[0]['lot_id'];
        $dataDeatail['user_id']  = $user_id;
        $dataDeatail['merge_pack_id'] = $dataLabel[0]['merge_pack_id'];
        $dataDeatail['quantity1'] = $data['qty1'];
        $dataDeatail['quantity2'] = $data['qty2'];
        $labelDetail = $this->updater->genSplitLabel($dataDeatail);

        $this->updater->updateLabel($labelID, $data);

        for ($i = 0; $i < sizeof($labelDetail); $i++) {
            $dataDetailSL['label_id'] = $labelDetail[$i]['id'];
            $dataDetailSL['split_label_id'] = $splitID;
            $this->updaterSpiteLabelDetail->insertSplitLabelDetailDeatilApi($dataDetailSL, $user_id);
        }

    

        
        $viewData = [
            'splitLabels' => $dataSplit,
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/splitLabels.twig', $viewData);
    }
}
