<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergeConfirmAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $session;
    private $mergeDetailFinder;
    private $labelUpdater;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        MergePackFinder $finder,
        Session $session,
        Responder $responder,
        MergePackUpdater  $updater,
        MergePackDetailFinder $mergeDetailFinder,
        LabelUpdater $labelUpdater
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->mergeDetailFinder = $mergeDetailFinder;
        $this->labelUpdater = $labelUpdater;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        $mergePackId =  $data['id'];
        $dataMergeDeatil['merge_pack_id'] = $mergePackId;
        $getMergeDeatil = $this->mergeDetailFinder->findMergePackDetails($dataMergeDeatil);
        $printerID = $data['printer_id'];
        $totalQty = 0;
        for ($i = 0; $i < sizeof($getMergeDeatil); $i++) {
            $Qty = $getMergeDeatil[$i]['quantity'];
            $totalQty = $totalQty + (int)$Qty;
        }

        $user_id = $this->session->get('user')["id"];
        $datalabel['merge_pack_id'] = $mergePackId;
        $datalabel['std_pack'] = $data['std_pack'];
        $datalabel['quantity'] = $totalQty;
        $datalabel['user_id'] = $user_id;
        $datalabel['product_id'] = $getMergeDeatil[0]['product_id'];
        $datalabel['printer_id'] = $printerID;
        $datalabel['wait_print'] = "Y";

        $this->labelUpdater->genMergeLabel($datalabel);

        for ($i = 0; $i < sizeof($getMergeDeatil); $i++) {
            $labelId = $getMergeDeatil[$i]['label_id'];
            $datalabel2['status'] = "VOID";
            $datalabel2['label_void_reason_id'] = "2";
            $this->labelUpdater->updateLabelApi($labelId, $datalabel2, $user_id);
        }
        
        $dataMergePack['merge_status'] = "PRINTED";
        $this->updater->updatePackMerge($mergePackId,$dataMergePack);

        return $this->responder->withRedirect($response, "merges");
    }
}
