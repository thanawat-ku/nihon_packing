<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class RegisterTagOnPackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findPack;
    private $finder;
    private $updatePack;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(PackFinder $findPack, PackUpdater $updatePack, InvoiceFinder $finder, Responder $responder)
    {

        $this->finder = $finder;
        $this->updatePack = $updatePack;
        $this->findPack = $findPack;
        $this->responder = $responder;
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

        $params = (array)$request->getQueryParams();
        $user_id = $params['user_id'];

        $arrPack = [];

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }

        if ($params['invoice_no'] != "") {
            $params['InvoiceNo'] = $params['invoice_no'];

            $rtIvoice = $this->finder->findInvoice($params);

            for ($i = 0; $i < count($rtIvoice); $i++) {
                $findPackingID['packing_id'] = $rtIvoice[$i]['PackingID'];
                $rtPack = $this->findPack->findPacks($findPackingID);
                if ($rtPack) {

                    if ($rtPack[0]['pack_status'] == "TAGGED") {
                        $upPack['pack_status'] = "INVOICED";

                        $upPack['invoice_no'] = $params['invoice_no'];

                        $this->updatePack->updatePackSyncApi((int)$rtPack[0]['id'], $upPack, $user_id);
                    }

                    $rtPack = $this->findPack->findPacks($findPackingID);
                    array_push($arrPack, $rtPack[0]);
                    $rtdata['packs'] = $arrPack;
                    // $rtdata['packs'] = $rtPack;
                }
            }
        } else {


            $rtdata['packs'] = $this->findPack->findPacks($params);
            $rtdata['message'] = "Get Pack Successful";
            $rtdata['error'] = false;

            return $this->responder->withJson($response, $rtdata);
        }
        if (!$rtIvoice) {
            $rtdata['message'] = "Get Pack Successful";
            $rtdata['error'] = true;
            return $this->responder->withJson($response, $rtdata);
        } else {
            $rtdata['message'] = "Get Pack Successful";
            $rtdata['error'] = false;

            return $this->responder->withJson($response, $rtdata);
        }
    }
}
