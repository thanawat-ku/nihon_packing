<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;

/**
 * Action.
 */
final class LotRegisterAction
{
    private $responder;
    private $updater;
    private $finder;
    private $labelFinder;
    private $labelUpdater;
    private $lnfpUpdate;
    private $lnfpFinder;
    private $session;


    public function __construct(
        Responder $responder,
        LotUpdater $updater,
        LotFinder $finder,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        LotNonFullyPackFinder $lnfpFinder,
        LotNonFullyPackUpdater $lnfpUpdate,
        Session $session,
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
        $this->lnfpUpdate = $lnfpUpdate;
        $this->lnfpFinder = $lnfpFinder;
        $this->session = $session;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data['lot_id'];
        $dataPack['status'] = "PACKED";

        $dataLot['lot_id'] = $lotId;
        $lot = $this->finder->findLots($dataLot);
        if ($lot[0]['status'] ==  "PRINTED") {
            $this->labelUpdater->registerLabel($lotId, $dataPack);
            $this->updater->registerLot($lotId, $dataPack);
        }

        $searchLnfp['search_prefer_lot_id'] = true;
        $searchLnfp['prefer_lot_id'] = $lotId;
        $rtLnfps = $this->lnfpFinder->findLotNonFullyPacks($searchLnfp);

        foreach ($rtLnfps as $rtLnfp) {
            $labelID = $rtLnfp['label_id'];
            $updateLnfp['is_register'] = "Y";
            $user_id = $this->session->get('user')["id"];
            $this->lnfpUpdate->updateLotNonFullyPack($labelID, $updateLnfp, $user_id);
        }
        $viewData = [];

        return $this->responder->withRedirect($response, "lots", $viewData);
    }
}
