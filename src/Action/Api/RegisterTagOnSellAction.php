<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class RegisterTagOnSellAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findSell;
    private $finder;
    private $updateSell;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(SellFinder $findSell, SellUpdater $updateSell, InvoiceFinder $finder, Responder $responder)
    {

        $this->finder = $finder;
        $this->updateSell = $updateSell;
        $this->findSell = $findSell;
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

        $arrSell = [];

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }

        if ($params['invoice_no'] != "") {
            $params['InvoiceNo'] = $params['invoice_no'];

            $rtIvoice = $this->finder->findInvoice($params);

            for ($i = 0; $i < count($rtIvoice); $i++) {
                $findPackingID['packing_id'] = $rtIvoice[$i]['PackingID'];
                $rtSell = $this->findSell->findSells($findPackingID);
                if ($rtSell) {

                    if ($rtSell[0]['sell_status'] == "TAGGED") {
                        $upSell['sell_status'] = "INVOICED";
                    }
                    $upSell['invoice_no'] = $params['invoice_no'];


                    $this->updateSell->updateSellSyncApi((int)$rtSell[0]['id'], $upSell, $user_id);
                    $rtSell = $this->findSell->findSells($findPackingID);
                    array_push($arrSell, $rtSell[0]);
                    $rtdata['sells'] = $arrSell;
                    // $rtdata['sells'] = $rtSell;
                }
            }
        } else {
            $rtdata['sells'] = $this->findSell->findSells($params);
            $rtdata['message'] = "Get Sell Successful";
            $rtdata['error'] = false;

            return $this->responder->withJson($response, $rtdata);
        }
        if (!$rtIvoice) {
            $rtdata['message'] = "Get Sell Successful";
            $rtdata['error'] = true;
            return $this->responder->withJson($response, $rtdata);
        } else {
            $rtdata['message'] = "Get Sell Successful";
            $rtdata['error'] = false;

            return $this->responder->withJson($response, $rtdata);
        }
    }
}
